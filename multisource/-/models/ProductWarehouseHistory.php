<?php namespace ss\multisource\models;

class ProductWarehouseHistory extends \Model
{
    public $table = 'ss_multisource_products_warehouses_history';

    public function product()
    {
        return $this->belongsTo(\ss\models\Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function pivot()
    {
        return $this->belongsTo(\ss\multisource\models\ProductWarehouse::class);
    }
}
