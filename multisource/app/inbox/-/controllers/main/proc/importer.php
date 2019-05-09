<?php namespace ss\multisource\app\inbox\controllers\main\proc;

class Importer extends \ss\multisource\app\inbox\controllers\main\proc\importer\AbstractController
{
    /**
     * @var \ewma\Process\AppProcess
     */
    private $process;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     */
    private $worksheet;

    private $count;

    private $n = 0;

    private $countLength;

    public function import()
    {
        $this->process = process();

        $this->aiPivot->import_proc_pid = $this->process->getPid();
        $this->aiPivot->save();

        pusher()->trigger('ss/multisource/inbox/importStart', [
            'aiPivotId' => $this->aiPivot->id,
            'xpid'      => $this->process->getXPid()
        ]);

        $this->log('START IMPORT importer=' . $this->importer->id . ' attachment=' . $this->attachment->id);

        if ($this->productsDeleteMode != 'disabled') {
            $this->deleteProducts();
        }

        $this->log('load rows...');
        $this->worksheet = $this->getSpreadsheet()->getSheet($this->aiPivot->sheet_index);

        $this->loadRows();
        $this->log('    ' . count($this->rows) . ' rows loaded');

        $this->renderIdRPads();

        $this->log('analyze...');
        list($catsTree, $productsBranchesByCatRowNumber) = $this->analyze();

        if ($catsTree) {
            $this->log('    tree');
            $this->importRecursion($catsTree, $productsBranchesByCatRowNumber);
        } else {
            $this->log('    flat');
            $this->importFlat($productsBranchesByCatRowNumber['']);
        }

        if ($this->warehouse && $this->productsDeleteMode == 'disabled' && $this->stockResetMode != 'disabled') {
            $this->resetStock();
        }

        pusher()->trigger('ss/multisource/inbox/importComplete', [
            'aiPivotId'       => $this->aiPivot->id,
            'attachmentXPack' => xpack_model($this->attachment)
        ]);

        $this->log('DONE IMPORT importer=' . $this->importer->id . ' attachment=' . $this->attachment->id);

        $this->aiPivot->import_proc_pid = false;
        $this->aiPivot->imported_at = \Carbon\Carbon::now()->toDateTimeString();
        $this->aiPivot->save();
    }

    private function importFlat($productsBranches)
    {
        $this->importProducts($productsBranches, $this->rootCat);
    }

    private function importRecursion($tree, $productsBranchesByCatRowNumber)
    {
        $logLSpaces = str_repeat(' ', $this->countLength * 2 + 1);

        $nameBranch = [];

        $recursion = function ($nodes, $cat) use (
            &$recursion,
            &$nameBranch,
            $logLSpaces,
            $productsBranchesByCatRowNumber
        ) {
            foreach ($nodes as $node => $subnodes) {
                $rowData = $this->getRowData($node);

                $name = $rowData['cat_name'];

                $nameBranch[] = $name;

                $localArticul = jmd5($nameBranch);

                if ($nestedCat = $cat->nested()->where('articul', $localArticul)->first()) {
                    $this->log($logLSpaces . '   SKIP CAT    : ' . $this->idRPad($nestedCat->id) . ' ' . a2p($nameBranch));
                } else {
                    $nestedCat = $cat->nested()->create([
                                                            'tree_id'     => $this->tree->id,
                                                            'articul'     => $localArticul,
                                                            'name'        => $name,
                                                            'remote_name' => $name
                                                        ]);

                    $this->log($logLSpaces . ' CREATE CAT    : ' . $this->idRPad($nestedCat->id) . ' ' . a2p($nameBranch));
                }

                $this->importProducts($productsBranchesByCatRowNumber[$node] ?? [], $nestedCat);

                $recursion($subnodes, $nestedCat);

                array_pop($nameBranch);
            }
        };

        $recursion($tree, $this->rootCat);
    }

    private $importedProductsIds = [];

    private function importProducts($branches, $cat)
    {
        foreach ($branches as $branch) {
            if (true === $this->process->handleIteration(10)) {
                break;
            }

            $name = [];
            $shortName = [];
            $key = [];

            $rowData = false;
            foreach ($branch as $rowNumber) {
                $rowData = $this->getRowData($rowNumber);

                $name[] = $rowData['name'];
                $shortName[] = $rowData['short_name'];
                $key[] = $rowData['key'];
            }

            $n = ++$this->n;

            $logN = str_pad($n, $this->countLength, ' ', STR_PAD_LEFT);

            if ($rowData) {
                $ignored = false;

                if ($this->productColumnsCheckMap) {
                    $ignored = $this->checkRowForProductByColumns($rowNumber) ? 'empty column' : false;
                }

                if ($this->productRequiredColors) {
                    $ignored = $this->checkRowForProductByColors($rowNumber) ? 'color' : false;
                }

                if ($ignored) {
                    $this->log($logN . '/' . $this->count . ' IGNORE ROW    : ' . $rowNumber . ' (' . $ignored . ')');
                } else {
                    $name = implode(' ', $name);
                    $shortName = implode(' ', $shortName);
                    $key = implode(' ', $key);

                    $localArticul = null;

                    if ($articul = $rowData['articul']) {
                        if ($this->articulZerofill) {
                            $localArticul = $this->articulPrefix . sprintf('%0' . $this->articulZerofill . 's', $articul);
                        } else {
                            $localArticul = $this->articulPrefix . $articul;
                        }
                    }

                    if ($product = $this->tree->products()->where('remote_key', $key)->first()) {
                        $updateData = [
                            'articul'           => $localArticul,
                            'remote_articul'    => $articul,
                            'remote_name'       => $name,
                            'remote_short_name' => $shortName,
                            'vendor_code'       => $rowData['vendor_code'],
                            'units'             => $rowData['units'],
                            'alt_units'         => $rowData['alt_units'],
                            'unit_size'         => $rowData['unit_size'],
                        ];

                        $updateData = array_filter($updateData, function ($value) {
                            return '' !== $value;
                        });

                        if ($product->cat_id != $cat->id) {
                            if (!$this->ignoreCatChange) {
                                $updateData['cat_id'] = $cat->id;

                                $operationLogLabel = 'UPD/MV';
                                $mvLogLabel = ' [change cat: ' . $product->cat_id . ' -> ' . $cat->id . ']';
                            } else {
                                $operationLogLabel = 'UPD/--';
                                $mvLogLabel = ' [change cat: ' . $product->cat_id . ' -> ' . $cat->id . ' IGNORED]';
                            }
                        } else {
                            $operationLogLabel = 'UPDATE';
                            $mvLogLabel = '';
                        }

                        $this->log($logN . '/' . $this->count . ' ' . $operationLogLabel . ' PRODUCT: ' . $this->idRPad($product->id) . '  └ ' . $product->remote_name . $mvLogLabel);

                        $product->update($updateData);
                    } else {
                        $createData = [
                            'tree_id'           => $this->tree->id,
                            'remote_key'        => $key,
                            'articul'           => $localArticul,
                            'remote_articul'    => $articul,
                            'name'              => $name,
                            'remote_name'       => $name,
                            'short_name'        => $shortName,
                            'remote_short_name' => $shortName,
                            'vendor_code'       => $rowData['vendor_code'],
                            'units'             => $rowData['units'],
                            'alt_units'         => $rowData['alt_units'],
                            'unit_size'         => $rowData['unit_size'],
                        ];

                        $product = $cat->products()->create($createData);

                        $this->log($logN . '/' . $this->count . ' CREATE PRODUCT: ' . $this->idRPad($product->id) . '  └ ' . $name);
                    }

                    $multisourceData = [
                        'division_id' => $this->importer->division_id,
                        'price'       => $rowData['price']
                    ];

                    if ($this->warehouse) {
                        $multisourceData['warehouse_id'] = $this->warehouse->id;
                        $multisourceData['stock'] = $rowData['stock'];
                    }

                    ss()->products->updateMultisourceData($product, $multisourceData, '', false);

                    $this->importedProductsIds[] = $product->id;
                }
            }

            $this->process->progress($n, $this->count, 'Импорт');
        }
    }

    private function deleteProducts()
    {
        $count = 0;

        if ($this->productsDeleteMode == 'tree') {
            $count = $this->tree->products()->count();

            $this->tree->products()->delete();
        }

        if ($this->productsDeleteMode == 'cat') {
            $catsIds = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $this->tree->id))->getIds($this->rootCat);

            $count = \ss\models\Product::whereIn('cat_id', $catsIds)->count();

            \ss\models\Product::whereIn('cat_id', $catsIds)->delete();
        }

        $this->log('PRODUCTS DELETED: ' . $count);
    }

    private function resetStock()
    {
        $this->process->progress(0, 0, 'Обнуление остатков');
        $this->process->handleIteration();

        $productsIds = [];

        if ($this->stockResetMode == 'tree') {
            $productsIds = table_ids($this->tree->products);
        }

        if ($this->stockResetMode == 'cat') {
            $catsIds = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $this->tree->id))->getIds($this->rootCat);

            $productsIds = table_ids(\ss\models\Product::whereIn('cat_id', $catsIds)->get());
        }

        diff($productsIds, $this->importedProductsIds);

        $products = \ss\models\Product::whereIn('id', $productsIds)->get();

        $count = count($products);
        $n = 0;

        if ($count) {
            $this->log('reset stock for ' . $count . ' products');

            $warehouseId = $this->warehouse->id;

            $divisionId = ss()->multisource->getDivisionIdByWarehouseId($warehouseId);

            foreach ($products as $product) {
                if (true === $this->process->handleIteration(10)) {
                    break;
                }

                $multisourceCache = _j($product->multisource_cache);

                $stock = $multisourceCache[$divisionId]['warehouses'][$warehouseId]['stock'] ?? 0;

                if ($stock != 0) {
                    ss()->products->updateMultisourceData($product, [
                        'warehouse_id' => $warehouseId,
                        'stock'        => 0
                    ], '', false);

                    $operationLogLabel = '     RESET STOCK';
                } else {
                    $operationLogLabel = 'SKIP RESET STOCK';
                }

                $this->log(++$n . '/' . $count . ' ' . $operationLogLabel . ': ' . $product->id);

                $this->process->progress($n, $count, 'Обнуление остатков');
            }
        } else {
            $this->log('does not have products for reset stock');
        }
    }

    //
    //
    //

    private function analyze()
    {
        list($catsBranches, $productsBranchesByCatRowNumber) = $this->getSeparatedBranches();

        $catsTree = $this->renderCatsTree($catsBranches);

        return [$catsTree, $productsBranchesByCatRowNumber];
    }

    private function renderCatsTree($branches)
    {
        $tree = [];

        foreach ($branches as $branch) {
            $node = &ap($tree, $branch);

            if (null === $node) {
                $node = [];
            }
        }

        return $tree;
    }

    private function getSeparatedBranches()
    {
        $branches = $this->getBranches();

        $this->count = count($branches);
        $this->countLength = strlen($this->count);

        $catsBranches = [];
        $productsBranchesByCatRowNumber = [];

        foreach ($branches as $branch) {
            if ($this->ignoreTreeView) {
                $productsBranchesByCatRowNumber[''][] = p2a(path_slice($branch, -$this->productNameLevels));
            } else {
                $catsBranches[] = path_slice($branch, 0, -$this->productNameLevels);

                $productCatRowNumber = path_slice($branch, -$this->productNameLevels - 1, -$this->productNameLevels);

                $productsBranchesByCatRowNumber[$productCatRowNumber][] = p2a(path_slice($branch, -$this->productNameLevels));
            }
        }

        $catsBranches = array_unique($catsBranches);

        return [$catsBranches, $productsBranchesByCatRowNumber];
    }

    private function getBranches()
    {
        $worksheet = $this->worksheet;

        //

        $count = -$this->skipRows;

        foreach ($worksheet->getRowIterator() as $row) {
            $count++;
        }

        $branches = [];
        $branch = [];

        $prevLevel = -1;
        $prevBranch = [];
        $prevIsProduct = false;

        $n = 0;
        foreach ($worksheet->getRowIterator() as $rowNumber => $row) {
            $n++;

            if ($n <= $this->skipRows) {
                continue;
            }

            $level = $worksheet->getRowDimension($rowNumber)->getOutlineLevel();

            $levelChange = $level - $prevLevel;

            $isProduct = -$levelChange >= $this->productNameLevels - 1 || $levelChange == 0 && $prevIsProduct;

            if ($levelChange == 0) {
                array_pop($branch);
            }

            if ($levelChange < 0) {
                for ($i = 0; $i <= -$levelChange; $i++) {
                    array_pop($branch);
                }
            }

            $branch[] = $rowNumber;

            if ($isProduct) {
                $branches[] = a2p($prevBranch);
            }

            $prevLevel = $level;
            $prevBranch = $branch;
            $prevIsProduct = $isProduct;
        }

        if ($this->lastRowIsProduct) {
            $branches[] = a2p($branch);
        }

        return $branches;
    }

    private function checkRowForProductByColumns($number)
    {
        $row = $this->renderRowArray($this->rows[$number]);

        foreach ($this->productColumnsCheckMap as $index) {
            if (empty($row[$index])) {
                return true;
            }
        }

        return false;
    }

    private function checkRowForProductByColors($number)
    {
        $row = $this->rows[$number];

        foreach ($row->getCellIterator() as $index => $cell) {
            if (true === $this->productRequiredColorsColumnsMap || in_array($index, $this->productRequiredColorsColumnsMap)) {
                $bg = $cell->getStyle()->getFill()->getStartColor()->getRGB();
                $fg = $cell->getStyle()->getFont()->getColor()->getRGB();

                if ($bg != $this->productRequiredColorsBg) {
                    return true;
                }

                if ($fg != $this->productRequiredColorsFg) {
                    return true;
                }
            }
        }
    }

    /**
     * @var $rows \PhpOffice\PhpSpreadsheet\Worksheet\Row[]
     */
    private $rows = [];

    private function loadRows()
    {
        $n = 0;
        foreach ($this->worksheet->getRowIterator() as $row) {
            $n++;

            $this->rows[$n] = $row;
        }
    }

    private function getRowData($number)
    {
        return $this->renderRowData($this->rows[$number]);
    }

    private $idPadLength;

    private function renderIdRPads()
    {
        $maxId = max(\ss\models\Product::max('id'), \ss\models\Cat::max('id'));

        $this->idPadLength = strlen($maxId);
    }

    private function idRPad($id)
    {
        return str_pad($id, $this->idPadLength);
    }
}
