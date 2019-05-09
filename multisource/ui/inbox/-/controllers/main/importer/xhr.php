<?php namespace ss\multisource\ui\inbox\controllers\main\importer;

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

    public function openCp()
    {
        if ($attachment = $this->unxpackModel('attachment')) {
            $this->c('\std\ui\dialogs~:open:importerCp, ss|ss/multisource/division', [
                'path'  => '@cp:view',
                'data'  => [
                    'attachment' => pack_model($attachment)
                ],
                'class' => 'padding'
            ]);
        }
    }
}
