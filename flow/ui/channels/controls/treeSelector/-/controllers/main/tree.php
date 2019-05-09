<?php namespace ss\flow\ui\channels\controls\treeSelector\controllers\main;

class Tree extends \Controller
{
    public function __create()
    {

    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $rootNode = ss()->trees->getRootNode();

        $addedTreesIds = table_cells(\ss\flow\models\Node::all(), 'tree_id');

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
                           'default'           => [

                           ],
                           'node_control'      => [
                               '>node:view|',
                               [
                                   'root_node_id'    => $rootNode->id,
                                   'node'            => '%model',
                                   'added_trees_ids' => $addedTreesIds
                               ]
                           ],
                           'query_builder'     => '>app:treeQueryBuilder',
                           'root_node_id'      => $rootNode->id,
                           'expand'            => false,
                           'sortable'          => false,
                           'movable'           => false,
                           'root_node_visible' => false,
                           'filter_ids'        => false
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
