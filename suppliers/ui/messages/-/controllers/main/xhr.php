<?php namespace ss\suppliers\ui\messages\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload|');
    }
}
