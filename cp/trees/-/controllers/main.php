<?php namespace ss\cp\trees\controllers;

class Main extends \Controller
{
    private $s;

    private $tree;

    public function __create()
    {
        $this->s = $this->s('|', [
            'selected_id' => false
        ]);

        $this->tree = \ss\models\Tree::find($this->s['selected_id']);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $tree = $this->tree;

        $v->assign([
                       'TREES_TREE' => $this->treesTreeView(),
                       'TREE'       => $this->c('tree~:view|', [
                           'tree' => $tree
                       ])
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:ss/trees');

        $this->css();

        $this->app->html->setFavicon(abs_url('-/ss/favicons/main.png'));

        return $v;
    }

    private function treesTreeView()
    {
        $rootNode = ss()->trees->getRootNode();

        return $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
            'default'          => [

            ],
            'node_control'     => [
                '>node:view|',
                [
                    'root_node_id' => $rootNode->id,
                    'node'         => '%model'
                ]
            ],
            'query_builder'    => ':treeQueryBuilder|',
            'root_node_id'     => $rootNode->id,
            'expand'           => false,
            'sortable'         => true,
            'movable'          => true,
            'selected_node_id' => $this->tree->id ?? false
        ]);
    }

    public function treeQueryBuilder()
    {
        return \ss\models\Tree::orderBy('position');
    }
}
