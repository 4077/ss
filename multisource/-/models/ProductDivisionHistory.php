<?php namespace ss\multisource\models;

class ProductDivisionHistory extends \Model
{
    public $table = 'ss_multisource_products_divisions_history';

    public function product()
    {
        return $this->belongsTo(\ss\models\Product::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function pivot()
    {
        return $this->belongsTo(\ss\multisource\models\ProductDivision::class);
    }
}
