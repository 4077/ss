<?php namespace ss\suppliers\messages\models;

class Attachment extends \Model
{
    public $table = 'ss_suppliers_attachments';

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
