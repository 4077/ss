<?php namespace ss\multisource\ui\mailboxes\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function create()
    {
        \ss\multisource\models\Mailbox::create([
                                                   'port' => 993
                                               ]);

        $this->c('~:reload');
    }

    public function open()
    {
        if ($mailbox = $this->unxpackModel('mailbox')) {
            $this->app->response->href(\ss\multisource\ui()->getRoute(path('mailboxes', $mailbox->id)));
        }
    }
}
