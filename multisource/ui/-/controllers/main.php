<?php namespace ss\multisource\ui\controllers;

class Main extends \Controller
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

    private function getTabs()
    {
        $division = $this->division;

        $tabs = [
            'divisions' => [
                'label'   => 'Подразделения',
                'ui_call' => $this->_abs('divisions~:view'),
                'class'   => 'padding'
            ],
            'mailboxes' => [
                'label'   => 'Почтовые ящики',
                'ui_call' => $this->_abs('mailboxes~:view'),
                'class'   => 'padding'
            ],
            'inbox'     => [
                'label'   => 'Входящие',
                'ui_call' => $this->_abs('inbox~:view'),
                'class'   => 'padding'
            ]
        ];

        return $tabs;
    }
}
