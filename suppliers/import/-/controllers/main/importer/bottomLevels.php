<?php namespace ss\suppliers\import\controllers\main\importer;

class BottomLevels extends AbstractImporterController
{
    protected $importer = 'bottomLevels';

    protected function import()
    {
        $rootCat = ss()->trees->getRootCat($this->tree->id);

        if ($spreadsheet = $this->getSpreadsheet()) {
            $worksheet = $spreadsheet->getActiveSheet();

            $skipRows = $this->data('skip_rows');

            $count = -$skipRows;
            foreach ($worksheet->getRowIterator() as $row) {
                $count++;
            }

            $prevLevel = -1;
            $prevRowData = [];
            $prevNameBranch = [];

//            $nameBranch = [];
//            $catBranch = [$rootCat];

            $currentCat = $rootCat;

            $catProducts = [];
            $nameBranch = [];

            $n = 0;
            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                $n++;

                if ($n <= $this->skipRows) {
                    continue;
                }

                $this->progress = $n / $count * 100;

                $row = $this->renderRowArray($row);

                //
                //
                //

                $level = $worksheet->getRowDimension($rowIndex)->getOutlineLevel();

                $rowData = [];

                remap($rowData, $row, $this->rowMap);

                $name = $rowData['name'] ?? '';

                $levelChange = $level - $prevLevel;

                if ($levelChange > 0) {
                    $nameBranch[] = $name;
                } elseif ($levelChange == 0) {
                    array_pop($nameBranch);

                    $nameBranch[] = $name;

                    $productData = $prevRowData;

                    $productName = implode(' ', array_slice($prevNameBranch, -$this->productLevels));

                    ra($productData, [
                        'name'    => $productName,
                        'articul' => $this->articulPrefix . md5($productName)
                    ]);

                    $catProducts[] = $productData;
                } else {
                    for ($i = 0; $i < -$levelChange + 1; $i++) {
                        array_pop($nameBranch);
                    }

                    $nameBranch[] = $name;

                    $productData = $prevRowData;

                    $productName = implode(' ', array_slice($prevNameBranch, -$this->productLevels));

                    ra($productData, [
                        'name'    => $productName,
                        'articul' => $this->articulPrefix . md5($productName)
                    ]);

                    $catProducts[] = $productData;

                    if ($levelChange + $this->productLevels - 1 != 0) {
                        $catBranch = array_slice($prevNameBranch, 0, -$this->productLevels);

                        $catArticul = $this->articulPrefix . jmd5($catBranch);

                        if ($cat = $this->tree->cats()->where('articul', $catArticul)->first()) {

                            $this->output('UPDATE CAT ' . implode('/', $catBranch));
                        } else {
                            $cat = $this->tree->cats()->create([
                                                                   'parent_id' => $currentCat->id,
                                                                   'articul'   => $catArticul,
                                                                   'name'      => implode('/', $catBranch),
                                                                   'type'      => $rootCat->type
                                                               ]);

                            $this->output('CREATE CAT ' . implode('/', $catBranch));
                        }

                        $this->importBottomLevels_write($cat, $catProducts);

                        $catProducts = [];
                    }
                }

                $prevLevel = $level;
                $prevRowData = $rowData;
                $prevNameBranch = $nameBranch;

                if ($this->app->mode == \ewma\App\App::REQUEST_MODE_CLI) {
                    print "\r" . $n . '/' . $count;
                }

                usleep(30000);
            }

            $this->output('COMPLETED');
        }
    }

    private function importBottomLevels_write($cat, $catProductsData)
    {
        foreach ($catProductsData as $productData) {
            $product = \ss\models\Product::where('remote_articul', $productData['articul'])
                ->where('tree_id', $this->tree->id)
                ->first();

            if (!$product) {
                remap($createData, $productData, $this->createMap);
                remap($createData, $productData, 'remote_id id, remote_articul articul, remote_name name');

                ra($createData, [
                    'tree_id' => $this->tree->id,
                    'articul' => $productData['articul'],
                ]);

                $product = $cat->products()->create($createData);

                $this->output('CREATE PRODUCT ' . $product['name']);
            } else {
                $updateData = [];

                remap($updateData, $productData, $this->updateMap);

                $product->update($updateData);

                $this->output('UPDATE PRODUCT ' . $product['name']);
            }

            ss()->products->updateMultisourceData($product, [
                'warehouse_id' => $this->warehouse->id,
                'price'        => $productData['price'] ?? null,
                'stock'        => $productData['stock'] ?? null,
                'reserved'     => $productData['reserved'] ?? null
            ]);
        }
    }
}
