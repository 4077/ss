<?php namespace ss\moderation\commander\bar\controllers;

class Main extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CONTENT' => false
                   ]);

        $this->css();

        return $v;
    }
}
