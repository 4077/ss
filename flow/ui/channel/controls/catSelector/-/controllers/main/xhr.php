<?php namespace ss\flow\ui\channel\controls\catSelector\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->c('~:reload', [
                'channel' => $channel,
                'type'    => $this->data('type')
            ]);
        }
    }
}
