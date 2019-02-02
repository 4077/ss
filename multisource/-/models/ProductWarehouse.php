<?php namespace ss\multisource\models;

class ProductWarehouse extends \Model
{
    public $table = 'ss_multisource_products_warehouses';

    public function product()
    {
        return $this->belongsTo(\ss\models\Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function history()
    {
        return $this->hasMany(ProductWarehouseHistory::class, 'pivot_id');
    }
}
