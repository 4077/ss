<?php namespace ss\multisource\ui\division\importersControls\articulZerofill\controllers;

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

        $items = [0 => 'Выключено'];

        for ($i = 4; $i <= 16; $i++) {
            $items[$i] = 'до ' . $i . ' символов';
        }

        $v->assign([
                       'CONTENT' => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:select',
                           'data'     => [
                               'importer' => $importerXPack
                           ],
                           'items'    => $items,
                           'selected' => $importer->articul_zerofill
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
