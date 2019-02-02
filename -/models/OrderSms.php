<?php namespace ss\models;

class OrderSms extends \Model
{
    protected $table = 'ss_orders_sms';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
