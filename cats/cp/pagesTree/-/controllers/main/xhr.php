<?php namespace ss\cats\cp\pagesTree\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('<:reload', [
                'cat' => $cat
            ]);
        }
    }
}
