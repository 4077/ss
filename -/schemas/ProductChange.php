<?php namespace ss\schemas;

class ProductChange extends \Schema
{
    public $table = 'ss_products';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('product_id')->default(0)->unsigned();
            $table->integer('tree_id')->default(0)->unsigned();
            $table->integer('root_cat_id')->default(0)->unsigned(); // todo del
            $table->integer('import_run_id')->default(0)->unsigned();
            $table->dateTime('datetime')->nullable();
            $table->enum('status', ['created', 'updated'])->default('created');
            $table->longText('data_before'); // ?
            $table->longText('data_after'); // ?

            $table->boolean('remote_cat_names_path_changed')->default(false);
            $table->string('remote_cat_names_path_before')->default('');
            $table->string('remote_cat_names_path_after')->default('');

            $table->boolean('remote_name_changed')->default(false);
            $table->string('remote_name_before')->default('');
            $table->string('remote_name_after')->default('');

            $table->boolean('price_changed')->default(false);
            $table->decimal('price_before', 14, 2)->nullable();
            $table->decimal('price_after', 14, 2)->nullable();

            $table->boolean('units_changed')->default(false);
            $table->string('units_before')->default('');
            $table->string('units_after')->default('');
        };
    }
}
