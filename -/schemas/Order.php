<?php namespace ss\schemas;

class Order extends \Schema
{
    public $table = 'ss_orders';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->timestamps();
            $table->integer('provider_id')->default(0)->unsigned(); // todo del
            $table->longText('items');
            $table->longText('delivery_data'); // todo del
        };
    }
}
