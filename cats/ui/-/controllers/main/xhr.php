<?php namespace ss\cats\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $this->c('<:reload', [
                'cat'         => $cat,
                'multisource' => _j64($this->data('multisource'))
            ]);
        }
    }

    public function pageClose()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            sstm()->events->trigger('cats/ui/pageClose', [
                'cat' => $cat
            ]);
        }
    }
}
