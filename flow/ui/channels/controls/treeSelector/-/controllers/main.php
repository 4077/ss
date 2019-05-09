<?php namespace ss\flow\ui\channels\controls\treeSelector\controllers;

class Main extends \Controller
{
    public function view()
    {
        return $this->c_('>tree:view');
    }
}
