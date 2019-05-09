<?php namespace ss\multisource\ui\division\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function selectTab()
    {
        if ($division = $this->unxpackModel('division')) {
            $this->s('~:tab', $this->data('tab'), RR);

            $this->c('~:reload', [], 'division');
        }
    }

    public function selectDivision()
    {
        $this->app->response->href(\ss\multisource\ui()->getRoute(path('divisions', $this->data('value'))));
    }
}
