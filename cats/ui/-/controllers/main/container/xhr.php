<?php namespace ss\cats\ui\controllers\main\container;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('<:reload', [
                'cat'         => $cat,
                'multisource' => _j64($this->data('multisource'))
            ]);
        }
    }

    public function containerDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {
            if (ss()->cats->isEditable($cat)) {
                $this->c('\ss\cats\cp dialogs:container|ss/cats', [
                    'cat' => $cat
                ]);
            }
        }
    }
}
