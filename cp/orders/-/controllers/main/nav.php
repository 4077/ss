<?php namespace ss\cp\orders\controllers\main;

class Nav extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $v->assign([
                       'CONTENT' => false
                   ]);

        $this->css();

        return $v;
    }
}