<?php namespace ss\Svc;

class Products extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    public function getName(\ss\models\Product $product)
    {
        return $product->name ?: ($product->short_name ?: '');
    }

    public function getShortName(\ss\models\Product $product)
    {
        return $product->short_name ?: ($product->name ?: '');
    }

    public function isEditable(\ss\models\Product $product)
    {
        $ssc = ssc();

        return $ssc->a('products/edit') || ($ssc->a('products/edit/in_own_cats') && $this->svc->own->isCatOwn($product->tree_id, $product->cat_id));
    }

    public function isCDable(\ss\models\Product $product)
    {
        $ssc = ssc();

        return $ssc->a('products/cd') || ($ssc->a('products/cd/in_own_cats') && $this->svc->own->isCatOwn($product->tree_id, $product->cat_id));
    }

    public function getCartKey($product)
    {
        return jmd5([$product->id]);
    }

    public function dropCache(\ss\models\Product $product)
    {
        $cacheDirPath = abs_path(
            'cache/ss/views',
            'tree_' . $product->tree_id,
            'cat_' . $product->cat_id,
            'product_' . $product->id
        );

        delete_dir($cacheDirPath);
    }

    public function update($product, $data, $treesConnectionsInstance = '')
    {
        $updatedProducts = [$product];
        $updatedProductsIds = [$product->id];

        $product->update($data);

        // todo кто и когда

        $updateDescendants = function ($product) use (&$updateDescendants, $data, $treesConnectionsInstance, &$updatedProducts, &$updatedProductsIds) {
            $descendants = ss()->trees->connections->getDescendants($product->tree, $treesConnectionsInstance);

            foreach ($descendants as $descendant) {
                if ($targetProduct = \ss\models\Product::where('source_id', $product->id)->where('tree_id', $descendant->target_id)->first()) {
                    if (!in_array($targetProduct->id, $updatedProductsIds)) {
                        $map = array_keys(array_filter(ss()->trees->connections->adapterData($descendant, 'products', 'st') ?? []));

                        $updateData = map($data, $map);

                        $targetProduct->update($updateData);

                        $updatedProducts[] = $targetProduct;
                        $updatedProductsIds[] = $targetProduct->id;
                    }

                    $updateDescendants($targetProduct);
                }
            }
        };

        $updateAscendants = function ($product) use (&$updateAscendants, $updateDescendants, $data, $treesConnectionsInstance, &$updatedProducts, &$updatedProductsIds) {
            $ascendants = ss()->trees->connections->getAscendants($product->tree, $treesConnectionsInstance);

            foreach ($ascendants as $ascendant) {
                if ($sourceProduct = \ss\models\Product::where('id', $product->source_id)->where('tree_id', $ascendant->source_id)->first()) {
                    $map = array_keys(array_filter(ss()->trees->connections->adapterData($ascendant, 'products', 'ts') ?? []));

                    $updateData = map($data, $map);

                    $sourceProduct->update($updateData);

                    $updatedProducts[] = $sourceProduct;
                    $updatedProductsIds[] = $sourceProduct->id;

                    $updateAscendants($sourceProduct);
                    $updateDescendants($sourceProduct);
                }
            }
        };

        $updateDescendants($product);
        $updateAscendants($product);

        foreach ($updatedProducts as $updatedProduct) {
            ss()->products->dropCache($updatedProduct);
        }

        return $updatedProducts;
    }

    /**
     * Если в настройках связи выбрано хотя бы одно из полей наличие и резерв, считается что выбраны оба поля
     *
     * @param        $product
     * @param        $data
     *                      строгий формат: // todo сделать возможность передачи null вместо значений
     *                      division_id
     *                      warehouse_id
     *                      stock
     *                      price
     *                      reserved
     *
     * @param string $treesConnectionsInstance
     *
     * @return array
     */
    public function updateMultisourceData(\ss\models\Product $product, $data, $treesConnectionsInstance = '')
    {
        $updatedProducts = [$product];
        $updatedProductsIds = [$product->id];

        $this->updateMultisourceDivisionData($product, $data['division_id'], $data['price']);
        $this->updateMultisourceWarehouseData($product, $data['warehouse_id'], $data['stock'], $data['reserved']);

        $this->updateMultisourceCache($product);

        $updateDescendants = function ($product) use (&$updateDescendants, $data, $treesConnectionsInstance, &$updatedProducts, &$updatedProductsIds) {
            $descendants = ss()->trees->connections->getDescendants($product->tree, $treesConnectionsInstance);

            foreach ($descendants as $descendant) {
                if ($targetProduct = \ss\models\Product::where('source_id', $product->id)->where('tree_id', $descendant->target_id)->first()) {
                    if (!in_array($targetProduct->id, $updatedProductsIds)) {
                        $map = array_keys(array_filter(ss()->trees->connections->adapterData($descendant, 'products', 'st') ?? []));

                        $productUpdated = false;

                        if (in_array('price', $map)) {
                            $this->updateMultisourceDivisionData($targetProduct, $data['division_id'], $data['price']);

                            $productUpdated = true;
                        }

                        if (in_array('stock', $map) || in_array('reserved', $map)) {
                            $this->updateMultisourceWarehouseData($targetProduct, $data['warehouse_id'], $data['stock'], $data['reserved']);

                            $productUpdated = true;
                        }

                        if ($productUpdated) {
                            $this->updateMultisourceCache($targetProduct);

                            $updatedProducts[] = $targetProduct;
                            $updatedProductsIds[] = $targetProduct->id;
                        }
                    }

                    $updateDescendants($targetProduct);
                }
            }
        };

        $updateAscendants = function ($product) use (&$updateAscendants, $data, $updateDescendants, $treesConnectionsInstance, &$updatedProducts, &$updatedProductsIds) {
            $ascendants = ss()->trees->connections->getAscendants($product->tree, $treesConnectionsInstance);

            foreach ($ascendants as $ascendant) {
                if ($sourceProduct = \ss\models\Product::where('id', $product->source_id)->where('tree_id', $ascendant->source_id)->first()) {
                    $map = array_keys(array_filter(ss()->trees->connections->adapterData($ascendant, 'products', 'ts') ?? []));

                    $productUpdated = false;

                    if (in_array('price', $map)) {
                        $this->updateMultisourceDivisionData($sourceProduct, $data['division_id'], $data['price']);

                        $productUpdated = true;
                    }

                    if (in_array('stock', $map) || in_array('reserved', $map)) {
                        $this->updateMultisourceWarehouseData($sourceProduct, $data['warehouse_id'], $data['stock'], $data['reserved']);

                        $productUpdated = true;
                    }

                    if ($productUpdated) {
                        $this->updateMultisourceCache($sourceProduct);

                        $updatedProducts[] = $sourceProduct;
                        $updatedProductsIds[] = $sourceProduct->id;
                    }

                    $updateAscendants($sourceProduct);
                    $updateDescendants($sourceProduct);
                }
            }
        };

        $updateDescendants($product);
        $updateAscendants($product);

        $this->clearMultisourceSummary($updatedProductsIds);

        return $updatedProducts;
    }

    public function updateMultisourceDivisionData(\ss\models\Product $product, $divisionId, $price)
    {
        if (null !== $price) {
            $pivot = \ss\multisource\models\ProductDivision::where('product_id', $product->id)->where('division_id', $divisionId)->first();

            if ($pivot) {
                if ($pivot->price != $price) {
                    $pivot->price = $price;
                    $pivot->save();

                    $pivot->history()->create([
                                                  'datetime' => \Carbon\Carbon::now()->toDateTimeString(),
                                                  'price'    => $price
                                              ]);
                }
            } else {
                \ss\multisource\models\ProductDivision::create([
                                                                   'product_id'  => $product->id,
                                                                   'division_id' => $divisionId,
                                                                   'price'       => $price
                                                               ]);
            }
        }
    }

    public function updateMultisourceWarehouseData(\ss\models\Product $product, $warehouseId, $stock, $reserved)
    {
        $pivot = \ss\multisource\models\ProductWarehouse::where('product_id', $product->id)->where('warehouse_id', $warehouseId)->first();

        if ($pivot) {
            $changed = false;

            $historyData = [];

            if (null !== $stock && $pivot->stock != $stock) {
                $pivot->stock = $stock;
                $historyData['stock'] = $stock;

                $changed = true;
            }

            if (null !== $reserved && $pivot->reserved != $reserved) {
                $pivot->reserved = $reserved;
                $historyData['reserved'] = $reserved;

                $changed = true;
            }

            if ($changed) {
                $historyData['datetime'] = \Carbon\Carbon::now()->toDateTimeString();

                $pivot->history()->create($historyData);
                $pivot->save();
            }
        } else {
            if (null !== $stock || null !== $reserved) {
                \ss\multisource\models\ProductWarehouse::create([
                                                                    'product_id'   => $product->id,
                                                                    'warehouse_id' => $warehouseId,
                                                                    'stock'        => $stock,
                                                                    'reserved'     => $reserved
                                                                ]);
            }
        }
    }

    /**
     * @param $input array|\ss\models\Product
     */
    public function clearMultisourceSummary($input)
    {
        $productsIds = [];

        if ($input instanceof \ss\models\Product) {
            $productsIds = [$input->id];
        } elseif (is_array($input)) {
            $productsIds = $input;
        }

        \ss\models\ProductsMultisourceSummary::whereIn('product_id', $productsIds)->delete();

        appc()->log('clear multisource summary: ' . a2l($productsIds));
    }

    public function updateImages($product, $treesConnectionsInstance = '')
    {
        $updatedProducts = [$product];
        $updatedProductsIds = [$product->id];

        $product->images_cache = '';
        $product->save();

        $updateDescendants = function ($product) use (&$updateDescendants, $treesConnectionsInstance, &$updatedProducts, &$updatedProductsIds) {
            $descendants = ss()->trees->connections->getDescendants($product->tree, $treesConnectionsInstance);

            foreach ($descendants as $descendant) {
                if ($targetProduct = \ss\models\Product::where('source_id', $product->id)->where('tree_id', $descendant->target_id)->first()) {
                    if (!in_array($targetProduct->id, $updatedProductsIds)) {
                        $map = array_keys(array_filter(ss()->trees->connections->adapterData($descendant, 'products', 'st') ?? []));

                        if (in_array('images', $map)) {
                            appc('\std\images~:delete', [
                                'model' => $targetProduct
                            ]);

                            appc('\std\images~:copy', [
                                'source' => $product,
                                'target' => $targetProduct
                            ]);

                            $targetProduct->images_cache = '';
                            $targetProduct->save();

                            $updatedProducts[] = $targetProduct;
                            $updatedProductsIds[] = $targetProduct->id;
                        }
                    }

                    $updateDescendants($targetProduct);
                }
            }
        };

        $updateAscendants = function ($product) use (&$updateAscendants, $updateDescendants, $treesConnectionsInstance, &$updatedProducts, &$updatedProductsIds) {
            $ascendants = ss()->trees->connections->getAscendants($product->tree, $treesConnectionsInstance);

            foreach ($ascendants as $ascendant) {
                if ($sourceProduct = \ss\models\Product::where('id', $product->source_id)->where('tree_id', $ascendant->source_id)->first()) {
                    $map = array_keys(array_filter(ss()->trees->connections->adapterData($ascendant, 'products', 'ts') ?? []));

                    if (in_array('images', $map)) {
                        appc('\std\images~:delete', [
                            'model' => $sourceProduct
                        ]);

                        appc('\std\images~:copy', [
                            'source' => $product,
                            'target' => $sourceProduct
                        ]);

                        $sourceProduct->images_cache = '';
                        $sourceProduct->save();

                        $updatedProducts[] = $sourceProduct;
                        $updatedProductsIds[] = $sourceProduct->id;
                    }

                    $updateAscendants($sourceProduct);
                    $updateDescendants($sourceProduct);
                }
            }
        };

        $updateDescendants($product);
        $updateAscendants($product);

        return $updatedProducts;
    }

    /**
     * @param $input \ss\models\Product|array|integer
     *
     * @return \ss\Svc\Products\RefsInfo
     */
    public function getRefsInfo($input)
    {
        $refsInfo = new \ss\Svc\Products\RefsInfo($input);

        return $refsInfo->render();
    }

    public function delete()
    {

    }

    public function updateSearchIndex(\ss\models\Product $product)
    {
        $cache[] = $product->name;
        $cache[] = $product->articul;
        $cache[] = $product->vendor_code;

        $cache = implode(' ', $cache);

//        $cache .= ' ' . $product->search_keywords;
//        $cache .= $this->svc->cats->getName($product->cat);
//        $cache .= $product->cat->search_keywords ? ' ' . $product->cat->search_keywords : '';

//        if ($product->props) {
//            $props = _j($product->props);
//
//            foreach ($props as $prop) {
//                $cache .= ' ' . $prop['value'];
//            }
//        }

        $product->search_index = $this->svc->search->query->getIndex($cache);
        $product->save();
    }

    private $divisions;

    private function getDivisions()
    {
        if (null === $this->divisions) {
            $this->divisions = table_rows_by_id(\ss\multisource\models\Division::orderBy('position')->get());
        }

        return $this->divisions;
    }

    private $warehouses;

    public function getWarehouses()
    {
        if (null === $this->warehouses) {
            $this->warehouses = table_rows_by_id(\ss\multisource\models\Warehouse::orderBy('position')->get());
        }

        return $this->warehouses;
    }

    public function updateMultisourceCache(\ss\models\Product $product)
    {
        $divisions = $this->getDivisions();
        $warehouses = $this->getWarehouses();

        $divisionsIds = array_keys($divisions);
        $warehousesIds = array_keys($warehouses);

        $sourceProduct = $product;

        do {
            $onDivisions = \ss\multisource\models\ProductDivision::where('product_id', $sourceProduct->id)->get();
            $onWarehouses = \ss\multisource\models\ProductWarehouse::where('product_id', $sourceProduct->id)->get();

            $sourceProduct = $sourceProduct->source;
        } while (!count($onDivisions) && !count($onWarehouses) && null !== $sourceProduct);

        $byDivisionId = table_rows_by($onDivisions, 'division_id');
        $byWarehouseId = table_rows_by($onWarehouses, 'warehouse_id');

        $multisourceCache = [];

        foreach ($byWarehouseId as $warehouseId => $pivot) {
            $stock = $byWarehouseId[$warehouseId]->stock ?? 0;
            $reserved = $byWarehouseId[$warehouseId]->reserved ?? 0;

            $divisionId = $warehouses[$warehouseId]->target_id;

            if (!isset($multisourceCache[$divisionId]['total_stock'])) {
                $multisourceCache[$divisionId]['total_stock'] = 0;
            }

            if (!isset($multisourceCache[$divisionId]['total_reserved'])) {
                $multisourceCache[$divisionId]['total_reserved'] = 0;
            }

            if ($stock || $reserved) {
                $multisourceCache[$divisionId]['total_stock'] += $stock;
                $multisourceCache[$divisionId]['total_reserved'] += $reserved;

                $multisourceCache[$divisionId]['warehouses'][$warehouseId]['stock'] = $stock;
                $multisourceCache[$divisionId]['warehouses'][$warehouseId]['reserved'] = $reserved;
            }
        }

        foreach ($byDivisionId as $divisionId => $pivot) {
            $price = $byDivisionId[$divisionId]->price ?? 0;

            $multisourceCache[$divisionId]['price'] = $price;

            aa($multisourceCache[$divisionId], [
                'total_reserved' => 0,
                'total_stock'    => 0,
                'warehouses'     => []
            ]);

            if (isset($multisourceCache[$divisionId]['warehouses'])) {
                $multisourceCache[$divisionId]['warehouses'] = map($multisourceCache[$divisionId]['warehouses'], $warehousesIds);
            }
        }

        $multisourceCache = map($multisourceCache, $divisionsIds);

        $product->multisource_cache = j_($multisourceCache);
        $product->save();
    }

    public function getMultisourceInstance($stockWarehouses, $underOrderWarehouses)
    {
        merge($stockWarehouses);
        merge($underOrderWarehouses);

        return jmd5([$stockWarehouses, $underOrderWarehouses]);
    }

    public function updateMultisourceSummary(\ss\models\Product $product, $stockWarehouses, $underOrderWarehouses)
    {
        $multisourceInstance = $this->getMultisourceInstance($stockWarehouses, $underOrderWarehouses);

        $summary = $this->getMultisourceSummary($product, $multisourceInstance);

        $stock = 0;
        $underOrder = 0;

        $stockMinPrice = PHP_INT_MAX;
        $stockMaxPrice = 0;

        $underOrderMinPrice = PHP_INT_MAX;
        $underOrderMaxPrice = 0;

        $multisourceCache = _j($product->multisource_cache);

        foreach ($multisourceCache as $divisionId => $divisionData) {
            $warehousesData = map($divisionData['warehouses'], $stockWarehouses);

            foreach ($warehousesData as $warehouseId => $warehouseData) {
                $stock += $warehouseData['stock'];
            }

            $stockMinPrice = min($stockMinPrice, $divisionData['price']);
            $stockMaxPrice = max($stockMaxPrice, $divisionData['price']);
        }

        foreach ($multisourceCache as $divisionId => $divisionData) {
            $warehousesData = map($divisionData['warehouses'], $underOrderWarehouses);

            foreach ($warehousesData as $warehouseId => $warehouseData) {
                $underOrder += $warehouseData['stock'];
            }

            $underOrderMinPrice = min($underOrderMinPrice, $divisionData['price']);
            $underOrderMaxPrice = max($underOrderMaxPrice, $divisionData['price']);
        }

        $summary->stock = $stock;
        $summary->under_order = $underOrder;

        $summary->stock_min_price = $stockMinPrice == PHP_INT_MAX ? 0 : $stockMinPrice;
        $summary->stock_max_price = $stockMaxPrice;

        $summary->under_order_min_price = $underOrderMinPrice == PHP_INT_MAX ? 0 : $underOrderMinPrice;
        $summary->under_order_max_price = $underOrderMaxPrice;

        $summary->save();

        return $summary;
    }

    public function getMultisourceSummary(\ss\models\Product $product, $multisourceInstance)
    {
        if (!$summary = $product->multisourceSummary()->where('instance', $multisourceInstance)->first()) {
            $summary = $product->multisourceSummary()->create([
                                                                  'instance' => $multisourceInstance
                                                              ]);
        }

        return $summary;
    }

    // todo при $checkSources=true в циклах тяжело
    public function explodeMultisourceCache(\ss\models\Product $product, $divisionId, $warehouseId, $checkSources = false)
    {
        $multisourceData = _j($product->multisource_cache) ?: [];

        if (!$multisourceData && $checkSources) {
            $source = $product;

            do {
                $multisourceData = _j($source->multisource_cache);
            } while (!$multisourceData && $source = $source->source);
        }

        $priceRange = false;

        if ($divisionId && $warehouseId) {
            $price = $multisourceData[$divisionId]['price'] ?? 0;
            $stock = $multisourceData[$divisionId]['warehouses'][$warehouseId]['stock'] ?? 0;
            $reserved = $multisourceData[$divisionId]['warehouses'][$warehouseId]['reserved'] ?? 0;
        } else {
            if ($divisionId) {
                $price = $multisourceData[$divisionId]['price'] ?? 0;
                $stock = $multisourceData[$divisionId]['total_stock'] ?? 0;
                $reserved = $multisourceData[$divisionId]['total_reserved'] ?? 0;
            } elseif ($warehouseId) {
                $price = $multisourceData[$divisionId]['price'] ?? 0;
                $stock = $multisourceData[$divisionId]['warehouses'][$warehouseId]['stock'] ?? 0;
                $reserved = $multisourceData[$divisionId]['warehouses'][$warehouseId]['reserved'] ?? 0;
            } else {
                $priceMin = PHP_INT_MAX;
                $priceMax = 0;

                $price = 0;
                $stock = 0;
                $reserved = 0;

                foreach ($multisourceData as $_divisionId => $divisionData) {
                    $stock += $divisionData['total_stock'] ?? 0;
                    $reserved += $divisionData['total_reserved'] ?? 0;

                    $priceMin = min($priceMin, $divisionData['price'] ?? PHP_INT_MAX);
                    $priceMax = max($priceMax, $divisionData['price'] ?? 0);
                }

                if ($priceMin == PHP_INT_MAX) {
                    $priceMin = 0;
                }

                if ($priceMin == 0 && $priceMax == 0) {
                    $priceRange = ' — ';
                } else {
                    $priceRange = $priceMin != $priceMax ? number_format__($priceMin) . ' — ' . number_format__($priceMax) : number_format__($priceMin);
                }
            }
        }

        return [$priceRange ?: number_format__($price), trim_zeros(number_format__($stock)), trim_zeros(number_format__($reserved))];
    }
}
