<?php namespace ss\flow\models;

class Collation extends \Model
{
    public $table = 'ss_flow_collation';

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function product()
    {
        return $this->belongsTo(\ss\models\Product::class, 'product_id');
    }
}
