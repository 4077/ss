<?php namespace ss\cp\orders\controllers\main;

class Orders extends \Controller
{
    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $v->assign([
                       'GRID' => $this->c('>grid:view')
                   ]);

        $this->css(':\css\std~');

        return $v;
    }
}
