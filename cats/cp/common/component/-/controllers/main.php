<?php namespace ss\cats\cp\common\component\controllers;

class Main extends \Controller
{
    private $pivot;

    private $instance;

    public function __create()
    {
        $this->pivot = $this->unpackModel('pivot');
        $this->instance = $this->data('instance');

        if ($this->pivot) {
            $this->instance_($this->pivot->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign('CONTENT', ss()->cats->renderComponentPivot($this->pivot, $this->instance));

        $this->css();

        return $v;
    }
}
