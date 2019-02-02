<?php namespace ss\cats\cp\pageNode\controllers\main\page;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reloadPageUi()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\ss\cats\ui~:reload', [
                'cat' => $cat
            ]);
        }
    }

    public function pageDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\ss\cats\cp dialogs:page|ss/cats', [
                'cat' => $cat
            ]);
        }
    }
}
