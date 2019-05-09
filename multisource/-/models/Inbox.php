<?php namespace ss\multisource\models;

class Inbox extends \Model
{
    public $table = 'ss_multisource_inbox';

    public function mailbox()
    {
        return $this->belongsTo(Mailbox::class);
    }

    public function attachments()
    {
        return $this->hasMany(InboxAttachment::class, 'message_id');
    }
}
