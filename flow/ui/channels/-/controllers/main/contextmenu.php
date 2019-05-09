<?php namespace ss\flow\ui\channels\controllers\main;

class Contextmenu extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'ADD_TREE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:openTreeSelector',
                           'class' => 'add_tree_button',
                           'label' => 'Добавить ветку',
                           'icon'  => 'fa fa-code-fork'
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
