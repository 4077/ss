<?php namespace ss\multisource\ui\divisions\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function create()
    {
        \ss\multisource\models\Division::create([]);

        $this->c('<:reload');
    }

    public function open()
    {
        if ($division = $this->unxpackModel('division')) {
            $this->app->response->href(\ss\multisource\ui()->getRoute(path('divisions', $division->id)));
        }
    }
}
