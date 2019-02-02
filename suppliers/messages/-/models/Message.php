<?php namespace ss\suppliers\messages\models;

class Message extends \Model
{
    public $table = 'ss_suppliers_messages';

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
