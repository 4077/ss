<?php namespace ss\schemas;

class OrderClient extends \Schema
{
    public $table = 'ss_orders_clients';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('order_id')->default(0)->unsigned();
            $table->string('organization')->default('');
            $table->string('fio')->default('');
            $table->string('email')->default('');
            $table->string('phone')->default('');
            $table->text('address');
            $table->text('comment');
        };
    }
}
