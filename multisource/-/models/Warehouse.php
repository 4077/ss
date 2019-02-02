<?php namespace ss\multisource\models;

class Warehouse extends \Model
{
    public $table = 'ss_multisource_warehouses';

    public function target()
    {
        return $this->morphTo();
    }
}
