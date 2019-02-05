<?php namespace ss\schemas;

class Product extends \Schema
{
    public $table = 'ss_products';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('cat_id')->default(0)->unsigned();
            $table->integer('tree_id')->default(0)->unsigned();
            $table->string('articul')->default('');
            $table->string('vendor_code')->default('');
            $table->integer('source_id')->default(0)->unsigned();
            $table->integer('remote_id')->default(0)->unsigned();
            $table->string('name')->default('');
            $table->string('short_name')->default('');
            $table->string('remote_articul')->default('');
            $table->string('remote_name')->default('');
            $table->string('remote_short_name')->default('');
            $table->text('remote_cat_names_path'); // todo deprecate
            $table->integer('position')->default(0)->unsigned();
            $table->boolean('enabled')->default(true);
            $table->boolean('enabled')->default(true);
            $table->boolean('published')->default(false);
            $table->enum('status', ['initial', 'temporary', 'scheduled', 'discarded', 'moderation'])->default('initial');
            $table->dateTime('status_datetime')->nullable();
            $table->string('import_name')->default(''); // todo deprecate (replace all to remote_name)
            $table->string('import_short_name')->default(''); // todo deprecate (replace all to remote_short_name)
            $table->decimal('stock', 14, 2)->nullable(); // todo del
            $table->decimal('reserved', 14, 2)->nullable(); // todo del
            $table->decimal('price', 14, 2)->nullable(); // todo del
            $table->string('units')->default('');
            $table->decimal('unit_size', 14, 7)->default(1);
            $table->decimal('alt_price', 14, 2)->nullable(); // todo del
            $table->string('alt_units')->default('');
            $table->decimal('old_price', 14, 2)->nullable(); // todo ?
            $table->dateTime('receipt_date')->nullable(); // todo ?
            $table->text('multisource_cache');
            $table->text('props');
            $table->boolean('searchable')->default(true);
            $table->text('search_keywords');
            $table->text('search_index');
            $table->text('images_cache');
            $table->text('description'); // todo ?
        };
    }
}
