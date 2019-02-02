<?php namespace ss\cats\cp\common\handler\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function toggleEnabled()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $cat->handler_enabled = !$cat->handler_enabled;
            $cat->save();

            // todo e()
            $this->c('<:reload', [
                'cat' => $cat
            ]);
        }
    }

    public function compile()
    {
        if ($handler = $this->unxpackModel('handler')) {
            handlers()->compile($handler);
        }
    }
}
