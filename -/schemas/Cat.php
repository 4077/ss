<?php namespace ss\schemas;

class Cat extends \Schema
{
    protected $table = 'ss_cats';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->default(0)->unsigned();
            $table->integer('container_id')->default(0)->unsigned();
            $table->integer('tree_id')->default(0)->unsigned();
            $table->string('articul')->default('');
            $table->integer('source_id')->default(0)->unsigned();
            $table->integer('remote_id')->default(0)->unsigned();
            $table->integer('remote_parent_id')->default(0)->unsigned();
            $table->enum('type', ['page', 'container'])->default('page');
            $table->string('name')->default('');
            $table->string('short_name')->default('');
            $table->string('remote_name')->default('');
            $table->string('alias')->default('');
            $table->boolean('alias_locked')->default(false);
            $table->text('route_cache');
            $table->integer('position')->default(0)->unsigned();
            $table->boolean('enabled')->default(true);
            $table->boolean('published')->default(false);
            $table->boolean('output_enabled')->default(true);
            $table->boolean('handler_enabled')->default(false);
            $table->text('data');
            $table->text('less');
            $table->text('stat');
            $table->text('description');
            $table->string('meta_title')->default('');
            $table->text('meta_keywords');
            $table->text('meta_description');
            $table->text('search_keywords');
            $table->text('images_cache');
        };
    }
}
