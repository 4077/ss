<?php namespace ss\multisource\cp\controllers;

class Main extends \Controller
{
    private $s;

    public function __create()
    {
        $this->s = $this->s(false, [
            'selected_division_id' => false
        ]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'DIVISIONS'               => handlers()->render('ss/multisource/ui/divisions:grid', [
                           'set' => [

                           ]
                       ]),
                       'DIVISION_CREATE_BUTTON'  => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createDivision',
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ]),
                       'WAREHOUSES'              => handlers()->render('ss/multisource/ui/warehouses:grid', [
                           'set' => [
                               'filter' => [
                                   'target_type' => \ss\multisource\models\Division::class,
                                   'target_id'   => $this->s['selected_division_id']
                               ]
                           ]
                       ]),
                       'WAREHOUSE_CREATE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createWarehouse',
                           'data'    => [

                           ],
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ]),
                       'GROUPS'                  => handlers()->render('ss/multisource/ui/groups:grid', [
//                           'set' => [
//
//                           ]
                       ]),
                       'GROUP_CREATE_BUTTON'     => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createGroup',
                           'data'    => [

                           ],
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ])
                   ]);

        $this->css(':\css\std~');

        return $v;
    }
}
