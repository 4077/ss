<?php namespace ss\flow\ui\channels\controllers\main\channelContextmenu;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function delete()
    {
        if ($channel = \ss\flow\models\Channel::find($this->data('channel_id'))) {
            $channel->delete();
        }
    }
}
