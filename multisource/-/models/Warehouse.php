<?php namespace ss\multisource\models;

class Warehouse extends \Model
{
    public $table = 'ss_multisource_warehouses';

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function group()
    {
        return $this->belongsTo(WarehouseGroup::class, 'warehouse_group_id');
    }
}

class WarehouseObserver
{
    public function creating(Warehouse $model)
    {
        $position = Warehouse::max('position') + 10;

        $model->position = $position;
    }
}

Warehouse::observe(new WarehouseObserver);
