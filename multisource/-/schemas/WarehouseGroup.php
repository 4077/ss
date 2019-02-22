<?php namespace ss\multisource\schemas;

class WarehouseGroup extends \Schema
{
    public $table = 'ss_multisource_warehouses_groups';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->morphs('target');
            $table->integer('position')->default(0)->unsigned();
            $table->string('name')->default('');
        };
    }
}
