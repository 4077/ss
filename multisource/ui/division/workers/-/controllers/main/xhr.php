<?php namespace ss\multisource\ui\division\workers\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function create()
    {
        if ($division = $this->unxpackModel('division')) {
            $division->workers()->create([]);

            $this->c('~:reload', [], 'division');
        }
    }
}
