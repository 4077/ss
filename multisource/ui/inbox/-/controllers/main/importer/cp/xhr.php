<?php namespace ss\multisource\ui\inbox\controllers\main\importer\cp;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($attachment = $this->unxpackModel('attachment')) {
            $this->c('<:reload', [
                'attachment' => $attachment
            ]);
        }
    }

    public function detectImporter()
    {
        if ($attachment = $this->unxpackModel('attachment')) {
            if ($this->data('sync')) {
                $this->c('^app/inbox~proc/importersDetector:detectAttachment', [
                    'attachment' => pack_model($attachment)
                ]);
            } else {
                $this->proc('^app/inbox~proc/importersDetector:detectAttachment', [
                    'attachment' => pack_model($attachment)
                ])->run()->wait();
            }
        }
    }


}
