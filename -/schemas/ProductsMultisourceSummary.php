<?php namespace ss\schemas;

class ProductsMultisourceSummary extends \Schema
{
    public $table = 'ss_multisource_summary';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('product_id')->default(0)->unsigned();
            $table->string('instance')->default('');
            $table->decimal('stock', 14, 2)->default(0);
            $table->decimal('under_order', 14, 2)->default(0);
            $table->decimal('stock_min_price', 14, 2)->default(0);
            $table->decimal('stock_max_price', 14, 2)->default(0);
            $table->decimal('under_order_min_price', 14, 2)->default(0);
            $table->decimal('under_order_max_price', 14, 2)->default(0);
        };
    }
}
