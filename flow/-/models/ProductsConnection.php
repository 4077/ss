<?php namespace ss\flow\models;

class ProductsConnection extends \Model
{
    public $table = 'ss_flow_products_connections';

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function source()
    {
        return $this->belongsTo(\ss\models\Product::class, 'source_id');
    }

    public function target()
    {
        return $this->belongsTo(\ss\models\Product::class, 'target_id');
    }
}
