<?php namespace ss\multisource\ui\division\warehouses\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function create()
    {
        if ($division = $this->unxpackModel('division')) {
            $division->warehouses()->create([]);

            $this->c('<:reload', [
                'division' => $division
            ]);
        }
    }
}
