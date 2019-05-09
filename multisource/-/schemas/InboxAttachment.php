<?php namespace ss\multisource\schemas;

class Inbox extends \Schema
{
    public $table = 'ss_multisource_inbox_attachments';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('message_id')->default(0);
            $table->char('md5', 32)->default('');
            $table->char('sha1', 40)->default('');
            $table->string('file_path')->default('');
            $table->integer('file_size')->default(0);
            $table->string('name')->default('');
            $table->string('importer')->nullable();
            $table->date('imported_at')->nullable();
        };
    }
}
