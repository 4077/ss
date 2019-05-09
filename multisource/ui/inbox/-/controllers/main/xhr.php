<?php namespace ss\multisource\ui\inbox\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload|');
    }

    public function setPage($page)
    {
        $this->s('~:page|', $page, RR);

        $this->reload();
    }

    public function setPerPage()
    {
        $this->s('~:per_page|', $this->data('value'), RR);

        $this->reload();
    }

    public function detectImporters()
    {
        /**
         * @var $process \ewma\Process\Process
         */
        $process = $this->c('^app/inbox~:detectImporters');

        if ($process) {
            $this->app->response->json(['xpid' => $process->getXPid()]);
        }
    }

    public function openMessage()
    {
        if ($message = \ss\multisource\models\Inbox::find($this->data('message_id'))) {
            $this->c('\std\ui\dialogs~:open:inboxMessage, ss|ss/multisource/division', [
                'path'    => '@message:view',
                'data'    => [
                    'message' => pack_model($message)
                ],
                'title'   => 'uid: ' . $message->uid,
                'default' => [
                    'pluginOptions' => [
                        'width'  => 500,
                        'height' => 600
                    ]
                ]
            ]);
        }
    }
}
