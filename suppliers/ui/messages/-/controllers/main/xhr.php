<?php namespace ss\suppliers\ui\messages\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload|');
    }

    public function setPage($page)
    {
        $this->s('~:page|', $page, RR);

        $this->reload();
    }

    public function setPerPage()
    {
        $this->s('~:per_page|', $this->data('value'), RR);

        $this->reload();
    }
}
