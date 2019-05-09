<?php namespace ss\multisource\ui\division\importer\controllers;

class Main extends \Controller
{
    private $importer;

    public function __create()
    {
        if ($this->importer = $this->unpackModel('importer')) {
            $this->instance_($this->importer->id);
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
                       'CONTENT' => handlers()->render('ss/multisource/ui/importer:form', [
                           'importer' => $this->importer
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
