<?php namespace ss\multisource\ui\division\importersControls\treeSelector\controllers;

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

        if ($tree = $importer->tree) {
            $v->assign('selected', [
                'ICON_CLASS' => $tree->mode == 'folders' ? 'fa fa-folder' : 'fa fa-file',
                'NAME'       => $tree->name
            ]);
        }

        $this->css();

        if (!$this->app->html->containerAdded($this->_nodeId())) {
            $this->app->html->addContainer($this->_nodeId(), $this->c('eventsDispatcher:view'));
        }

        return $v;
    }
}
