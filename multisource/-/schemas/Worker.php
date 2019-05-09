<?php namespace ss\multisource\schemas;

class Worker extends \Schema
{
    public $table = 'ss_multisource_divisions_workers';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('division_id');
            $table->integer('position')->default(0)->unsigned();
            $table->string('name')->default('');
            $table->char('phone', 11)->default('');
            $table->text('emails');
        };
    }
}
