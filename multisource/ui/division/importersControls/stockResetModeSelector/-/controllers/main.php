<?php namespace ss\multisource\ui\division\importersControls\stockResetModeSelector\controllers;

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
                           'items'    => [
                               'disabled' => 'Выключено',
                               'tree'     => 'В пределах ветки',
                               'cat'      => 'В пределах категории'
                           ],
                           'selected' => $importer->stock_reset_mode
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
