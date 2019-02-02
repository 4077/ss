<?php namespace ss\multisource\models;

class ProductDivision extends \Model
{
    public $table = 'ss_multisource_products_divisions';

    public function product()
    {
        return $this->belongsTo(\ss\models\Product::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function history()
    {
        return $this->hasMany(ProductDivisionHistory::class, 'pivot_id');
    }
}
