<?php namespace ss\controllers;

class Router extends \Controller implements \ewma\Interfaces\RouterInterface
{
    public function getResponse()
    {
        $this->route('dsfa/*')->to(ssc()->_p('site/dsfa~:view'));

        $this->route('ss/login/form_submit')->to(ssc()->_p('site/auth~login:formSubmit'));
        $this->route('ss/restore/form_submit')->to(ssc()->_p('site/auth~restore:formSubmit'));

        return $this->routeResponse();
    }
}
