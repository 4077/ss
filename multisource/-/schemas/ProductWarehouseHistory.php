<?php namespace ss\multisource\schemas;

class ProductWarehouseHistory extends \Schema
{
    public $table = 'ss_multisource_products_warehouses_history';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('pivot_id')->default(0)->unsigned();
            $table->integer('product_id')->default(0)->unsigned();
            $table->integer('warehouse_id')->default(0)->unsigned();
            $table->dateTime('datetime');
            $table->decimal('stock', 14, 2)->nullable();
            $table->decimal('reserved', 14, 2)->nullable();
        };
    }
}
