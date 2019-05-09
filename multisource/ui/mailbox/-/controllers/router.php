<?php namespace ss\multisource\ui\mailbox\controllers;

class Router extends \Controller implements \ewma\Interfaces\RouterInterface
{
    public function getResponse()
    {
        $this->route('download/{md5}/{sha1}')->to(':download');
        $this->route('last')->to(':lastMailboxView');
        $this->route('{mailbox_id}')->to(':mailboxView');

        return $this->routeResponse();
    }

    public function lastMailboxView()
    {
        $lastMailBoxId = $this->s(':last_mailbox_id');

        if (!$lastMailBoxId) {
            if ($lastCreatedMailBox = \ss\multisource\models\Mailbox::orderBy('id', 'DESC')->first()) {
                $lastMailBoxId = $lastCreatedMailBox->id;
            }
        }

        $this->data('mailbox_id', $lastMailBoxId);

        return $this->mailboxView();
    }

    public function mailboxView()
    {
        if ($mailbox = \ss\multisource\models\Mailbox::find($this->data('mailbox_id'))) {
            $this->s(':last_mailbox_id', $mailbox->id, RR);

            return $this->c('~:view', [
                'mailbox' => $mailbox
            ]);
        }
    }

    public function download()
    {
        $attachment = \ss\multisource\models\InboxAttachment::where('md5', $this->data('md5'))->where('sha1', $this->data('sha1'))->first();

        if ($attachment) {
            $filePath = $this->_protected('data', '^app/inbox~:' . $attachment->file_path);

            if (file_exists($filePath)) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . ($attachment->name));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));

                readfile($filePath);

                exit;
            }
        }
    }
}
