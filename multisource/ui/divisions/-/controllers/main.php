<?php namespace ss\multisource\ui\divisions\controllers;

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
                       'CONTENT' => handlers()->render('ss/multisource/ui/divisions:grid', [
//                           'set' => [
//
//                           ]
                       ]),
                       'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ])
                   ]);

        $this->css(':\css\std~');

        return $v;
    }
}
