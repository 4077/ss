<?php namespace ss\cp\trees\tree\own\users\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateLoginFilter()
    {
        $s = &$this->s('<');

        $s['login_filter'] = $this->data('value');

        $this->c('~usersList:reload');
    }
}
