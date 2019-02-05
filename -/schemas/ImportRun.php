<?php namespace ss\schemas;

class ImportRun extends \Schema
{
    public $table = 'ss_import_runs';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('instance')->default('');
            $table->integer('target_cat_id')->default(0)->unsigned();
            $table->integer('tree_id')->default(0)->unsigned();
            $table->integer('number')->default(0)->unsigned();
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
        };
    }
}
