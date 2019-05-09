<?php namespace ss\multisource\ui\division\importersControls\treeSelector\controllers\main;

class Tree extends \Controller
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

        $rootNode = ss()->trees->getRootNode();

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
                           'default'           => [

                           ],
                           'node_control'      => [
                               '>node:view|',
                               [
                                   'root_node_id' => $rootNode->id,
                                   'node'         => '%model',
                                   'importer'     => xpack_model($importer)
                               ]
                           ],
                           'query_builder'     => '>app:treeQueryBuilder',
                           'root_node_id'      => $rootNode->id,
                           'expand'            => false,
                           'sortable'          => false,
                           'movable'           => false,
                           'selected_node_id'  => $importer->tree_id,
                           'root_node_visible' => false,
                           'filter_ids'        => false
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
