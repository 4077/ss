<?php namespace ss\cp\trees\tree\cats\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload|', [], 'tree');
    }
}
