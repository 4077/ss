<?php namespace ss\multisource\ui\division\importersControls\productNameLevelsSelector\controllers;

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

        $importer = $this->importer;
        $importerXPack = xpack_model($importer);

        $v->assign([
                       'CONTENT' => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:select',
                           'data'     => [
                               'importer' => $importerXPack
                           ],
                           'items'    => [1, 2, 3, 4],
                           'combine'  => true,
                           'selected' => $importer->product_name_levels
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
