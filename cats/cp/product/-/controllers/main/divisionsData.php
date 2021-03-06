<?php namespace ss\cats\cp\product\controllers\main;

class DivisionsData extends \Controller
{
    private $product;

    public function __create()
    {
        $this->product = $this->data('product');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $product = $this->product;

        $source = $product;

        do {
            $data = _j($source->multisource_cache);
        } while (!$data && $source = $source->source);

        $divisionsById = table_rows_by_id(\ss\multisource\models\Division::orderBy('position')->get());
        $warehousesById = table_rows_by_id(\ss\multisource\models\Warehouse::orderBy('position')->get());

        $divisionsIds = array_keys($divisionsById);
        $warehousesIds = array_keys($warehousesById);

        $multisourceCache = map($data, $divisionsIds);

        foreach ($multisourceCache as $divisionId => $divisionData) {
            if ($division = $divisionsById[$divisionId] ?? false) {
                $price = $divisionData['price'] ?? 0;

                $v->assign('division', [
                    'NAME'            => $division->name,
                    'PRICE'           => number_format__($price),
                    'TOTAL_STOCK'     => trim_zeros(number_format__($divisionData['total_stock'] ?? 0)),
                    'TOTAL_RESERVED'  => trim_zeros(number_format__($divisionData['total_reserved'] ?? 0)),
                    'TOTAL_AVAILABLE' => trim_zeros(number_format__(($divisionData['total_stock'] ?? 0) - ($divisionData['total_reserved'] ?? 0))),
                ]);

                $discount = $divisionData['discount'] ?? 0;

                if ($discount) {
                    $v->assign('division/discount', [
                        'VALUE'        => $discount,
                        'RESULT_PRICE' => number_format__($price - $price * $discount / 100)
                    ]);

                    if ($units = $product->units) {
                        $v->assign('division/discount/units', [
                            'VALUE' => $units
                        ]);
                    }
                }

                if ($units = $product->units) {
                    $v->assign('division/units', [
                        'VALUE' => $units
                    ]);
                }

                if (isset($divisionData['warehouses'])) {
                    foreach (map($divisionData['warehouses'], $warehousesIds) as $warehouseId => $warehouseData) {
                        $warehouse = $warehousesById[$warehouseId];

                        $stock = $warehouseData['stock'] ?? 0;

                        if ($stock > 0) {
                            $v->assign('division/warehouse', [
                                'NAME'      => $warehouse->name,
                                'STOCK'     => trim_zeros(number_format__($stock)),
                                'RESERVED'  => trim_zeros(number_format__($warehouseData['reserved'] ?? 0)),
                                'AVAILABLE' => trim_zeros(number_format__($stock - ($warehouseData['reserved'] ?? 0))),
                            ]);
                        }
                    }
                }
            }
        }

        $this->css();

        return $v;
    }
}
