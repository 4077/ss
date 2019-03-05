<?php namespace ss\multisource\models;

class Division extends \Model
{
    public $table = 'ss_multisource_divisions';

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}
