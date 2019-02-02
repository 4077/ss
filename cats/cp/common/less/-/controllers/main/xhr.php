<?php namespace ss\cats\cp\common\less\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload', [], 'cat');
    }
}
