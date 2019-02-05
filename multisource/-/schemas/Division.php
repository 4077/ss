<?php namespace ss\multisource\schemas;

class Division extends \Schema
{
    public $table = 'ss_multisource_divisions';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('position')->default(0)->unsigned();
            $table->string('name')->default('');
            $table->text('description');
        };
    }
}
