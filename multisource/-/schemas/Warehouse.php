<?php namespace ss\multisource\schemas;

class Warehouse extends \Schema
{
    public $table = 'ss_multisource_warehouses';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('division_id');
            $table->integer('group_id')->default(0);
            $table->integer('position')->default(0)->unsigned();
            $table->string('name')->default('');
            $table->text('description');
        };
    }
}
