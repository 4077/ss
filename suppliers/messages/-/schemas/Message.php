<?php namespace ss\suppliers\messages\schemas;

class Message extends \Schema
{
    public $table = 'ss_suppliers_messages';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('instance')->default('');
            $table->integer('uid')->default(0);
            $table->dateTime('datetime')->nullable();
            $table->text('subject');
            $table->mediumText('html_body');
            $table->mediumText('plaintext_body');
        };
    }
}
