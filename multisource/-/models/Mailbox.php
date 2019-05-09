<?php namespace ss\multisource\models;

class Mailbox extends \Model
{
    public $table = 'ss_multisource_mailboxes';

    public function messages()
    {
        return $this->hasMany(Inbox::class);
    }
}
