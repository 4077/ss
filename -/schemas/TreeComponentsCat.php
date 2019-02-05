<?php namespace ss\schemas;

class TreeComponentsCat extends \Schema
{
    public $table = 'ss_trees_components_cats';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('tree_id')->default(0)->unsigned();
            $table->integer('cat_id')->default(0)->unsigned();
            $table->string('cat_type')->default('');
            $table->enum('type', ['renderer', 'wrapper'])->default('renderer');
            $table->enum('type', ['none', 'merge', 'diff'])->default('none');
            $table->string('access')->default('');
        };
    }
}
