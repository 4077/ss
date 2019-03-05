<?php namespace ss\cats\controllers\main\info;

class Product extends \Controller
{
    public function get()
    {
        $product = $this->data['product'];

        return [
            'model'       => $product->toArray(),
            'multisource' => $this->multisource($product),
            'cat'         => $product->cat
                ? $this->c('@cat:get', [
                    'cat' => $product->cat
                ])
                : '-'
        ];
    }

    public function multisource($product)
    {
        return [
            'cache'   => _j($product->multisource_cache),
            'summary' => $product->multisourceSummary
        ];
    }

    public function history()
    {
        $product = $this->data['product'];
        $warehouse = $this->data['warehouse'];

        $division = $warehouse->division;

        $divisionHistory = \ss\multisource\models\ProductDivisionHistory::where('product_id', $product->id)
            ->where('division_id', $division->id)
            ->orderBy('datetime', 'DESC')
            ->get();

        $warehouseHistory = \ss\multisource\models\ProductWarehouseHistory::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->orderBy('datetime', 'DESC')
            ->get();

        $divisionHistory = table_rows_by($divisionHistory, 'datetime');
        $warehouseHistory = table_rows_by($warehouseHistory, 'datetime');

        $divisionHistoryDatetimes = array_keys($divisionHistory);
        $warehouseHistoryDatetimes = array_keys($warehouseHistory);

        $datetimes = [];

        merge($datetimes, $divisionHistoryDatetimes);
        merge($datetimes, $warehouseHistoryDatetimes);

        $output = [];

        foreach ($datetimes as $datetime) {
            $divisionHistoryRecord = [];
            $warehouseHistoryRecord = [];

            if (isset($divisionHistory[$datetime])) {
                $divisionHistoryRecord = $divisionHistory[$datetime]->toArray();
            }

            if (isset($warehouseHistory[$datetime])) {
                $warehouseHistoryRecord = $warehouseHistory[$datetime]->toArray();
            }

            $output[$datetime] = [];
            $outputRecord = &$output[$datetime];

            remap($outputRecord, $divisionHistoryRecord, 'price, discount');
            remap($outputRecord, $warehouseHistoryRecord, 'stock, reserved');
        }

        return $output;
    }
}
