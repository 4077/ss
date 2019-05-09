<?php namespace ss\flow\ui\channel\controls\fieldPreprocessor\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload', [], 'channel, type');
    }
}
