<?php namespace ss\suppliers\import\controllers\main\importer;

class Normal extends AbstractImporterController
{
    protected $importer = 'normal';

    protected function import()
    {
        $rootCat = ss()->trees->getRootCat($this->tree->id);

        if ($spreadsheet = $this->getSpreadsheet()) {
            $worksheet = $spreadsheet->getActiveSheet();

            $count = 0;
            foreach ($worksheet->getRowIterator() as $row) {
                $count++;
            }

            $prevLevel = -1;

            if ($this->baseCatId) {
                $catBranch = \ewma\Data\Tree::getBranch(\ss\models\Cat::find($this->baseCatId));
                $nameBranch = array_slice(table_column($catBranch, 'name'), 1);
            } else {
                $catBranch = [$rootCat];
                $nameBranch = [];
            }

            $currentCat = end($catBranch);

            if ($articulZerofill = $this->articulZerofill) {
                if (is_int($articulZerofill)) {
                    $articulZerofillRule = '%0' . $articulZerofill . 's';
                } else {
                    $articulZerofillRule = '%08s';
                }
            } else {
                $articulZerofillRule = false;
            }

            $process = $this->app->process;

            $n = 0;
            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                if ($process->handleIteration(50)) {
                    break;
                }

                $n++;

                if ($this->testRows && !in_array($n, $this->testRows)) {
                    $this->log('SKIP row ' . $n . ' (test) ');

                    continue;
                }

                if ($n <= $this->skipRows) {
                    continue;
                }

                $row = $this->renderRowArray($row);

                if ($this->isProduct($row)) {
                    $productData = [];

                    remap($productData, $row, $this->rowMap);

                    $productData = array_map('trim', $productData);

                    $remoteKeyValue = trim($productData[$this->remoteKeyField]);

                    if ($articulZerofill) {
                        $articul = $this->articulPrefix . sprintf($articulZerofillRule, $remoteKeyValue);
                    } else {
                        $articul = $this->articulPrefix . $remoteKeyValue;
                    }

                    $remoteKeyField = 'remote_' . $this->remoteKeyField;

                    $product = \ss\models\Product::where($remoteKeyField, $remoteKeyValue)
                        ->where('tree_id', $this->tree->id)
                        ->first();

                    if (!$product) {
                        if ($this->createProducts) {
                            $createData = [];

                            remap($createData, $productData, $this->createMap);
                            remap($createData, $productData, 'remote_id id, remote_articul articul, remote_name name');

                            ra($createData, [
                                'tree_id' => $this->tree->id,
                                'articul' => $articul,
                            ]);

                            $product = $currentCat->products()->create($createData);

                            $this->log($n . '/' . $count . ' CREATE PRODUCT id=' . $product->id . ' name=' . $product->name);
                        } else {
                            $this->log($n . '/' . $count . ' SKIP (disabled)');
                        }
                    } else {
                        if ($this->updateProducts) {
                            $updateData = [];

                            remap($updateData, $productData, $this->updateMap);

                            ra($updateData, [
                                'tree_id' => $this->tree->id
                            ]);

                            if ($this->updateProductsCats) {
                                ra($updateData, [
                                    'cat_id' => $currentCat->id
                                ]);
                            }

                            $product->update($updateData);

                            $this->log($n . '/' . $count . ' UPDATE PRODUCT id=' . $product->id . ' name=' . $product->name);
                        } else {
                            $this->log($n . '/' . $count . ' SKIP (disabled) PRODUCT id=' . $product->id . ' name=' . $product->name);
                        }
                    }

                    if ($product && ($this->createProducts || $this->updateProducts)) {
                        $price = $productData['price'] ?? null;
                        $stock = $productData['stock'] ?? null;
                        $reserved = $productData['reserved'] ?? null;

                        if (null !== $price && null !== $this->priceMultiplier) {
                            $price *= $this->priceMultiplier;
                        }

                        ss()->products->updateMultisourceData($product, [
                            'warehouse_id' => $this->warehouse->id,
                            'price'        => $price,
                            'stock'        => $stock,
                            'reserved'     => $reserved
                        ]);

                        $this->log(str_repeat('-', strlen($n) + strlen($count) + 1) . ' UPDATE MULTISOURCE price=' . $price . ' stock=' . $stock . ' reserved=' . $reserved);
                    }
                } else {
                    if ($this->isCat($row)) {
                        $level = $worksheet->getRowDimension($rowIndex)->getOutlineLevel();

                        $catName = trim($this->getCatName($row));

                        if ($level > $prevLevel) {
                            $nameBranch[] = $catName;
                            $catArticul = $this->articulPrefix . jmd5($nameBranch);
                        } elseif ($level == $prevLevel) {
                            array_pop($nameBranch);
                            array_pop($catBranch);

                            $nameBranch[] = $catName;
                            $catArticul = $this->articulPrefix . jmd5($nameBranch);

                            $currentCat = end($catBranch);
                        } else {
                            for ($i = 0; $i < $prevLevel - $level + 1; $i++) {
                                array_pop($nameBranch);
                                array_pop($catBranch);
                            }

                            $nameBranch[] = $catName;
                            $catArticul = $this->articulPrefix . jmd5($nameBranch);

                            $currentCat = end($catBranch);
                        }

                        if ($cat = $this->tree->cats()->where('articul', $catArticul)->first()) {
                            if ($this->updateCats) {
                                if ($cat->parent_id != $currentCat->id) {
                                    $cat->parent_id = $currentCat->id;
                                    $cat->save();

                                    $this->log($n . '/' . $count . ' UPDATE CAT id=' . $cat->id . ' name=' . $cat->name);
                                } else {
                                    $this->log($n . '/' . $count . ' SKIP CAT id=' . $cat->id . ' name=' . $cat->name);
                                }
                            } else {
                                $this->log($n . '/' . $count . ' SKIP (disabled) CAT id=' . $cat->id . ' name=' . $cat->name);
                            }
                        } else {
                            if ($this->createCats) {
                                $cat = $this->tree->cats()->create([
                                                                       'parent_id' => $currentCat->id,
                                                                       'articul'   => $catArticul,
                                                                       'name'      => $catName,
                                                                       'type'      => $rootCat->type
                                                                   ]);

                                $this->log($n . '/' . $count . ' CREATE CAT id=' . $cat->id . ' name=' . $cat->name);
                            } else {
                                $this->log($n . '/' . $count . ' SKIP (disabled)');
                            }
                        }

                        if (null !== $cat) {
                            $catBranch[] = $cat;
                            $currentCat = $cat;
                        }

                        $prevLevel = $level;
                    }
                }

                $this->publicProcFileUpdate([
                                                'fileCode' => $this->fileCode,
                                                'current'  => $n,
                                                'total'    => $count
                                            ]);
            }

            return true;
        }
    }
}
