<?php namespace ss\multisource\schemas;

class DivisionsIntersection extends \Schema
{
    public $table = 'ss_multisource_divisions_intersections';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('source_id')->default(0)->unsigned();
            $table->integer('target_id')->default(0)->unsigned();
            $table->decimal('price_coefficient', 4, 2)->default(1);
        };
    }
}
