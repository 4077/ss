<?php namespace ss\multisource\models;

class ProductDivisionHistory extends \Model
{
    public $table = 'ss_multisource_products_divisions_history';

    public function pivot()
    {
        return $this->belongsTo(\ss\multisource\models\ProductDivision::class);
    }
}
