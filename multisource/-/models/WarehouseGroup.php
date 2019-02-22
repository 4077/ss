<?php namespace ss\multisource\models;

class WarehouseGroup extends \Model
{
    public $table = 'ss_multisource_warehouses_groups';

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'group_id');
    }
}
