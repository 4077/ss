<?php namespace ss\multisource\ui\division\warehouses\controllers;

class Main extends \Controller
{
    private $division;

    public function __create()
    {
        if ($this->division = $this->unpackModel('division')) {

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

        $division = $this->division;
        $divisionXPack = xpack_model($division);

        $v->assign([
                       'CONTENT'       => handlers()->render('ss/multisource/ui/warehouses:grid', [
                           'set' => [
                               'filter' => [
                                   'division_id' => $division->id
                               ]
                           ]
                       ]),
                       'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'data'    => [
                               'division' => $divisionXPack
                           ],
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ])
                   ]);

        $this->css(':\css\std~');

        return $v;
    }
}
