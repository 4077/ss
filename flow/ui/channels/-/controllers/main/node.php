<?php namespace ss\flow\ui\channels\controllers\main;

class Node extends \Controller
{
    private $node;

    public function __create()
    {
        if ($this->node = $this->unpackModel('node')) {
            $this->instance_($this->node->id);
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

        $node = $this->node;
        $tree = $node->tree;

        $v->assign([
                       'TREE_ICON_CLASS' => 'fa fa-' . ($tree->mode == 'folders' ? 'folder' : 'file'),
                       'TREE_NAME'       => $tree->name
                   ]);

        $this->css();

        return $v;
    }
}
