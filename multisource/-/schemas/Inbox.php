<?php namespace ss\multisource\schemas;

class Inbox extends \Schema
{
    public $table = 'ss_multisource_inbox';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('division_id');
            $table->integer('uid')->default(0)->unsigned();
            $table->dateTime('datetime');
            $table->string('from')->default('');
            $table->text('subject');
            $table->mediumText('html_body');
            $table->mediumText('plaintext_body');
        };
    }
}
