<?php namespace ss\schemas;

class CatComponent extends \Schema
{
    protected $table = 'ss_cats_components';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('cat_id')->default(0)->unsigned();
            $table->integer('component_id')->default(0)->unsigned();
            $table->enum('type', ['renderer', 'wrapper'])->default('renderer');
            $table->integer('position')->default(0)->unsigned();
            $table->boolean('enabled')->default(true);
            $table->boolean('pinned')->default(true);
            $table->text('data');
        };
    }
}
