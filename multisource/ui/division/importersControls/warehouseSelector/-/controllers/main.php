<?php namespace ss\multisource\ui\division\importersControls\warehouseSelector\controllers;

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

        $warehouses = $importer->division->warehouses()->orderBy('position')->get();

        $v->assign([
                       'CONTENT' => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:select',
                           'data'     => [
                               'importer' => $importerXPack
                           ],
                           'items'    => [0 => '-'] + table_cells_by_id($warehouses, 'name'),
                           'selected' => $importer->warehouse_id
                       ])
                   ]);

        $this->css();

        if (!$this->app->html->containerAdded($this->_nodeId())) {
            $this->app->html->addContainer($this->_nodeId(), $this->c('eventsDispatcher:view'));
        }

        return $v;
    }
}
