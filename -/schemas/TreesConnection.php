<?php namespace ss\schemas;

class TreesConnection extends \Schema
{
    public $table = 'ss_trees_connections';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('instance')->default('');
            $table->integer('source_id')->default(0)->unsigned();
            $table->integer('target_id')->default(0)->unsigned();
            $table->text('data');
        };
    }
}
