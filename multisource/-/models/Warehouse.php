<?php namespace ss\multisource\models;

class Warehouse extends \Model
{
    public $table = 'ss_multisource_warehouses';

    public function target()
    {
        return $this->morphTo();
    }

    public function group()
    {
        return $this->belongsTo(WarehouseGroup::class, 'group_id');
    }
}
