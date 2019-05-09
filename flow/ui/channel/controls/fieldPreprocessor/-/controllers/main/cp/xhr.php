<?php namespace ss\flow\ui\channel\controls\fieldPreprocessor\controllers\main\cp;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload', [], 'channel, type');
    }

    public function reset()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $type = $this->data('type');

            $this->c('\ewma\nodeFileEditor~:reset|ss/flow/' . $channel->id . '/fieldPreprocessor/' . $type);

            pusher()->trigger('ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/save');
        }
    }

    public function save()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $type = $this->data('type');

            $this->c('\ewma\nodeFileEditor~:save|ss/flow/' . $channel->id . '/fieldPreprocessor/' . $type);

            pusher()->trigger('ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/reset');
        }
    }
}
