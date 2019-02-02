<?php namespace ss\cats\cp\product\controllers;

// todo del

class DivisionsData extends \Controller
{
    private $product;

    public function __create()
    {
        $this->product = $this->unpackModel('product') or $this->lock();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $product = $this->product;
        $productXPack = xpack_model($product);

        $divisions = table_rows_by_id(\ss\multisource\models\Division::orderBy('position')->get());
        $warehouses = table_rows_by_id(\ss\multisource\models\Warehouse::orderBy('position')->get());

        $divisionsIds = array_keys($divisions);
        $warehousesIds = array_keys($warehouses);

        $sourceProduct = $product;

        do {
            $onDivisions = \ss\multisource\models\ProductDivision::where('product_id', $sourceProduct->id)->get();
            $onWarehouses = \ss\multisource\models\ProductWarehouse::where('product_id', $sourceProduct->id)->get();

            $sourceProduct = $product->source;
        } while (!count($onDivisions) && !count($onWarehouses) && null !== $sourceProduct);

        $byDivisionId = table_rows_by($onDivisions, 'division_id');
        $byWarehouseId = table_rows_by($onWarehouses, 'warehouse_id');

        //
        //
        //

        $byDivisionsSummary = [];

        foreach ($byWarehouseId as $warehouseId => $pivot) {
            $stock = $byWarehouseId[$warehouseId]->stock ?? 0;
            $reserved = $byWarehouseId[$warehouseId]->reserved ?? 0;

            if ($stock || $reserved) {
                $divisionId = $warehouses[$warehouseId]->target_id;

                $byDivisionsSummary[$divisionId][$warehouseId]['stock'] = $stock;
                $byDivisionsSummary[$divisionId][$warehouseId]['reserved'] = $reserved;
            }
        }

        foreach ($byDivisionId as $divisionId => $pivot) {
            $price = $byDivisionId[$divisionId]->price ?? 0;

            $byDivisionsSummary[$divisionId]['price'] = $price;
        }

        //
        //
        //

        $byDivisionsSummary = map($byDivisionsSummary, $divisionsIds);

        foreach ($byDivisionsSummary as $divisionId => $data) {
            if ($divisions[$divisionId]) {
                $v->assign('division', [
                    'NAME'           => $divisions[$divisionId]->name,
                    'PRICE'          => $data['price'],
                    'HISTORY_BUTTON' => $this->c('\std\ui button:view', [
                        'path'    => '>xhr:historyDialog|',
                        'data'    => [
                            'product' => $productXPack,
                            'type'    => 'division',
                            'id'      => $divisionId
                        ],
                        'class'   => '',
                        'content' => $data['price']
                    ])
                ]);
            }

            $warehousesData = map($byDivisionsSummary[$divisionId], $warehousesIds);

            foreach ($warehousesData as $warehouseId => $warehouseData) {
                if (isset($warehouses[$warehouseId])) {
                    $v->assign('division/warehouse', [
                        'NAME'           => $warehouses[$warehouseId]->name,
                        'HISTORY_BUTTON' => $this->c('\std\ui button:view', [
                            'path'    => '>xhr:historyDialog|',
                            'data'    => [
                                'product' => $productXPack,
                                'type'    => 'warehouse',
                                'id'      => $warehouseId
                            ],
                            'class'   => '',
                            'content' => $warehouseData['reserved'] . '/' . $warehouseData['stock']
                        ])
                    ]);
                }
            }
        }

        $v->assign([
                       'DATA' => $this->c('\std\ui\data~:view|' . $this->_nodeInstance(), [
                           'read_call' => $this->_abs(':readMultisourceCache', ['product' => pack_model($product)]),
                           //                'write_call' => $this->_abs('>app:writeOutputData', ['call' => $callPack])
                       ])
                   ]);

        $this->css();

        return $v;
    }

    public function readMultisourceCache()
    {
        return _j($this->product->multisource_cache);
    }
}
