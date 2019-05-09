<?php namespace ss\flow\ui\channel\controls\posthandler\controllers\main\cp;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload', [], 'channel');
    }

    public function reset()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->c('\ewma\nodeFileEditor~:reset|ss/flow/' . $channel->id . '/posthandler');

            pusher()->trigger('ss/flow/posthandler/channel_' . $channel->id . '/save');
        }
    }

    public function save()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->c('\ewma\nodeFileEditor~:save|ss/flow/' . $channel->id . '/posthandler');

            pusher()->trigger('ss/flow/posthandler/channel_' . $channel->id . '/reset');
        }
    }
}
