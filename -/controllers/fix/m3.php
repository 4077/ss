<?php namespace ss\controllers\fix;

class M3 extends \Controller
{
    public function multisourceHistoryNewFields()
    {
        return false;

        $warehousesHistory = \ss\multisource\models\ProductWarehouseHistory::with('pivot')->get();

        $n = 0;
        $c = count($warehousesHistory);

        foreach ($warehousesHistory as $record) {
            $record->product_id = $record->pivot->product_id;
            $record->warehouse_id = $record->pivot->warehouse_id;
            $record->save();

            $this->log('w: ' . ++$n . '/' . $c);
        }

        $divisionsHistory = \ss\multisource\models\ProductDivisionHistory::with('pivot')->get();

        $n = 0;
        $c = count($divisionsHistory);

        foreach ($divisionsHistory as $record) {
            $record->product_id = $record->pivot->product_id;
            $record->division_id = $record->pivot->division_id;
            $record->save();

            $this->log('d: ' . ++$n . '/' . $c);
        }
    }

    public function xpack()
    {
        return j64_(\ss\models\Cat::class . ':75566', true);
    }
}
