<?php namespace ss\flow\ui\channel\controls\posthandler\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload', [], 'channel, type');
    }
}
