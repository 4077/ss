<?php namespace ss\models;

use SleepingOwl\WithJoin\WithJoinTrait;

class Order extends \Model
{
    use WithJoinTrait;

    protected $table = 'ss_orders';

    public $timestamps = true;

    public function client()
    {
        return $this->hasOne(OrderClient::class);
    }

    public function sms()
    {
        return $this->hasMany(OrderSms::class);
    }

    public function delete()
    {
        $this->client()->delete();

        parent::delete();
    }
}
