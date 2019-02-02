<?php namespace ss\cp\controllers;

class AccessDenied extends \Controller
{
    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CONTENT' => 'ACCESS DENIED'
                   ]);

        $this->css();

        return $v;
    }
}
