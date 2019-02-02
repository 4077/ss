<?php namespace ss\cats\cp\controllers;

class AccessDenied extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $v->assign([
                       'CONTENT' => $this->data('label') ?: 'нет доступа'
                   ]);

        $this->css();

        return $v;
    }
}
