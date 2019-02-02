<?php namespace ss\models;

class OrderClient extends \Model
{
    protected $table = 'ss_orders_clients';

    public function client()
    {
        return $this->belongsTo(Order::class);
    }
}
