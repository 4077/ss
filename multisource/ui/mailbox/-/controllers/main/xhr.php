<?php namespace ss\multisource\ui\mailbox\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function update()
    {
        if ($mailbox = $this->unxpackModel('mailbox')) {
            if ($this->data('sync')) {
                if ($lastMessage = $mailbox->messages()->orderBy('datetime', 'DESC')->first()) {
                    $since = \Carbon\Carbon::parse($lastMessage->datetime)->subDay()->format('d F Y');
                } else {
                    $since = \Carbon\Carbon::create(2019, 3, 5)->format('d F Y');
                }

                $this->c('^app/inbox~proc/messagesLoader:run', [
                    'mailbox' => pack_model($mailbox),
                    'since'   => $since
                ]);
            } else {
                /**
                 * @var $process \ewma\Process\Process
                 */
                $process = $this->c('^app/inbox~:update', [
                    'mailbox_id' => $mailbox->id
                ]);

                if ($process) {
                    $this->app->response->json(['xpid' => $process->getXPid()]);
                }
            }
        }
    }
}
