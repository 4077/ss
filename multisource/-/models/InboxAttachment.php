<?php namespace ss\multisource\models;

class InboxAttachment extends \Model
{
    public $table = 'ss_multisource_inbox_attachments';

    public function message()
    {
        return $this->belongsTo(Inbox::class);
    }

    public function importer() // todo del
    {
        return $this->belongsTo(Importer::class);
    }
}
