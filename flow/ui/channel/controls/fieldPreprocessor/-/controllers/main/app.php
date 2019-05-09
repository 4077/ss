<?php namespace ss\flow\ui\channel\controls\fieldPreprocessor\controllers\main;

class App extends \Controller
{
    public function onSave()
    {
        $channel = $this->unpackModel('channel');
        $type = $this->data('type');

        pusher()->trigger('ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/save');
    }

    public function onReset()
    {
        $channel = $this->unpackModel('channel');
        $type = $this->data('type');

        pusher()->trigger('ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/reset');
    }

    public function onUpdate()
    {
        $channel = $this->unpackModel('channel');
        $type = $this->data('type');

        pusher()->triggerOthers('ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/update');
        pusher()->triggerSelf('ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/update-self');
    }
}
