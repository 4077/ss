<?php namespace ss\multisource\schemas;

class ProductDivisionHistory extends \Schema
{
    public $table = 'ss_multisource_products_divisions_history';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('pivot_id')->default(0)->unsigned();
            $table->dateTime('datetime');
            $table->decimal('price', 14, 2)->nullable();
            $table->tinyInteger('discount')->nullable();
        };
    }
}
