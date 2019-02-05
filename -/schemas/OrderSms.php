<?php namespace ss\schemas;

class OrderSms extends \Schema
{
    public $table = 'ss_orders_sms';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('order_id')->default(0)->unsigned();
            $table->integer('provider_id')->default(0)->unsigned(); // todo del
            $table->char('to', 11)->nullable();
            $table->char('discard_code', 8)->nullable();
            $table->dateTime('send_datetime')->nullable();
            $table->dateTime('sent')->nullable();
            $table->dateTime('discarded')->nullable();
            $table->text('message');
        };
    }
}
