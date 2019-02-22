<?php namespace ss\multisource\schemas;

class ProductDivision extends \Schema
{
    public $table = 'ss_multisource_products_divisions';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('product_id')->default(0)->unsigned();
            $table->integer('division_id')->default(0)->unsigned();
            $table->decimal('price', 14, 2)->nullable();
            $table->tinyInteger('discount')->nullable();
        };
    }
}
