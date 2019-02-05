<?php namespace ss\schemas;

class Tree extends \Schema
{
    public $table = 'ss_trees';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('parent_id')->default(0)->unsigned();
            $table->enum('mode', ['folders', 'pages'])->default('folders');
            $table->boolean('editable')->default(false);
            $table->integer('position')->default(0)->unsigned();
            $table->boolean('enabled')->default(true);
            $table->text('data');
            $table->string('name')->default('');
            $table->text('description');
        };
    }
}
