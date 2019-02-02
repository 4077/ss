<?php namespace ss\cp\trees\tree\components\controllers;

class Main extends \Controller
{
    private $s;

    private $tree;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();

        $this->s = $this->s(false, [
            'selected_cat_id' => false,
            'cat_type'        => 'container'
        ]);

        $this->s = $this->smap('|', 'type');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $s = $this->s;

        $tree = $this->tree;
        $treeXPack = xpack_model($tree);

        $v->assign([
                       'PAGE_BUTTON'      => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:setCatType|',
                           'data'  => [
                               'tree'     => $treeXPack,
                               'cat_type' => 'page'
                           ],
                           'class' => 'button ' . ($s['cat_type'] == 'page' ? 'pressed' : ''),
                           'icon'  => 'fa fa-file',
                           'label' => 'для страниц'
                       ]),
                       'CONTAINER_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:setCatType|',
                           'data'  => [
                               'tree'     => $treeXPack,
                               'cat_type' => 'container'
                           ],
                           'class' => 'button ' . ($s['cat_type'] == 'container' ? 'pressed' : ''),
                           'icon'  => 'fa fa-cube',
                           'label' => 'для контейнеров'
                       ]),
                       'TREE'             => $this->treeView()
                   ]);

        $this->css();

        $this->se('ss/trees/tree/components/cat_select')->rebind(':reload', ['tree' => pack_model($this->tree)]);

        return $v;
    }

    private function treeView()
    {
        $rootNode = components()->cats->getRootCat();

        $treeInfo = ss()->trees->getCompatibleComponentsTreeInfo($this->tree->id, $this->s['cat_type'], $this->s['type']);

        return $this->c('\std\ui\tree~:view|' . $this->_nodeId(), [
            'query_builder'     => '>app:getQueryBuilder',
            'node_control'      => [
                '>node:view',
                [
                    'cat'                    => '%model',
                    'cat_type'               => $this->s['cat_type'],
                    'type'                   => $this->s['type'],
                    'tree'                   => pack_model($this->tree),
                    'root_cat_id'            => $rootNode->id,
                    'enabled_ids'            => $treeInfo->enabledIds,
                    'auto_enabled_ids'       => $treeInfo->autoEnabledIds,
                    'has_nested_enabled_ids' => $treeInfo->hasNestedEnabledIds,
                    'merge_ids'              => $treeInfo->mergeIds,
                    'diff_ids'               => $treeInfo->diffIds,
                    'access_by_cat_id'       => $treeInfo->accessByCatId
                ]
            ],
            'root_node_id'      => $rootNode->id,
            'root_node_visible' => false,
            'selected_node_id'  => $this->s['selected_cat_id'],
            'movable'           => false,
            'sortable'          => false
        ]);
    }
}
