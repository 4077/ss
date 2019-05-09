<?php namespace ss\multisource\schemas;

class Importer extends \Schema
{
    public $table = 'ss_multisource_importers';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('division_id')->default(0)->unsigned();
            $table->integer('warehouse_id')->default(0)->unsigned();
            $table->integer('tree_id')->default(0)->unsigned();
            $table->integer('position')->default(0)->unsigned();
            $table->string('name')->default('');
            $table->string('articul_prefix')->default('');
            $table->tinyInteger('articul_zerofill')->default(0);
            $table->integer('skip_rows')->default(0);
            $table->text('detect_map');
            $table->text('import_map');
        };
    }
}
