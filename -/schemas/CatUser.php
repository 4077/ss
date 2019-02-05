<?php namespace ss\schemas;

class CatUser extends \Schema
{
    protected $table = 'ss_cats_users';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('cat_id')->default(0)->unsigned();
            $table->integer('user_id')->default(0)->unsigned();
            $table->enum('mode', ['MERGE', 'DIFF'])->default('MERGE');
        };
    }
}
