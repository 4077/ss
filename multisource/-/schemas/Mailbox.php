<?php namespace ss\multisource\schemas;

class Mailbox extends \Schema
{
    public $table = 'ss_multisource_inbox';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('position')->default(0)->unsigned();
            $table->string('host')->default('');
            $table->smallInteger('port')->default(993)->unsigned();
            $table->string('user');
            $table->string('pass');
        };
    }
}
