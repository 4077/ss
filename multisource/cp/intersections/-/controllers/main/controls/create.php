<?php namespace ss\multisource\cp\intersections\controllers\main\controls;

class Create extends \Controller
{
    private $source;

    private $target;

    public function __create()
    {
        $this->source = $this->data('source');
        $this->target = $this->data('target');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'data'    => [
                               'source' => xpack_model($this->source),
                               'target' => xpack_model($this->target)
                           ],
                           'class'   => 'button',
                           'content' => '+'
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
