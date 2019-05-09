<?php namespace ss\multisource\app\inbox\controllers;

class Main extends \Controller
{
    public function update()
    {
        if ($mailbox = \ss\multisource\models\Mailbox::find($this->data('mailbox_id'))) {
            if ($lastMessage = $mailbox->messages()->orderBy('datetime', 'DESC')->first()) {
                $since = \Carbon\Carbon::parse($lastMessage->datetime)->subDay()->format('d F Y');
            } else {
//                $since = \Carbon\Carbon::create(2018, 11, 1)->format('d F Y');
                $since = \Carbon\Carbon::create(2019, 3, 5)->format('d F Y');
            }

            $process = $this->proc('>proc/messagesLoader:run', [
                'mailbox' => pack_model($mailbox),
                'since'   => $since
            ])->pathLock()->run();

            if ($process) {
                $mailbox->update_pid = $process->getPid();
                $mailbox->save();

                pusher()->trigger('ss/multisource/inbox/updateStart', [
                    'xpid' => $process->getXPid()
                ]);

                return $process;
            } else {
                $mailbox->update_pid = false;
                $mailbox->save();
            }
        }
    }

    public function detectImporters()
    {
        if ($process = $this->proc('>proc/importersDetector:run')->pathLock()->run()) {
            $this->d(':xpids/detect_importers', $process->getPid(), RR);

            pusher()->trigger('ss/multisource/inbox/importersDetectionStart', [
                'xpid' => $process->getXPid()
            ]);

            return $process;
        }
    }

    public function import()
    {

    }
}
