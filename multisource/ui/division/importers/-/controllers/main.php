<?php namespace ss\multisource\ui\division\importers\controllers;

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

        $v->assign([
                       'CONTENT'       => handlers()->render('ss/multisource/ui/importers:grid', [
                           'set' => [
                               'filter' => [
                                   'division_id' => $this->division->id
                               ]
                           ]
                       ]),
                       'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'data'    => [
                               'division' => xpack_model($this->division)
                           ],
                           'class'   => 'create_button',
                           'content' => 'Создать'
                       ])
                   ]);

        $this->css(':\css\std~');

        return $v;
    }
}
