<?php namespace ss\flow\ui\channel\controls\posthandler\controllers\main;

class App extends \Controller
{
    public function onSave()
    {
        $channel = $this->unpackModel('channel');

        pusher()->trigger('ss/flow/posthandler/channel_' . $channel->id . '/save');
    }

    public function onReset()
    {
        $channel = $this->unpackModel('channel');

        pusher()->trigger('ss/flow/posthandler/channel_' . $channel->id . '/reset');
    }

    public function onUpdate()
    {
        $channel = $this->unpackModel('channel');

        pusher()->triggerOthers('ss/flow/posthandler/channel_' . $channel->id . '/update');
        pusher()->triggerSelf('ss/flow/posthandler/channel_' . $channel->id . '/update-self');
    }
}
