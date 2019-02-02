<?php namespace ss\cp\trees\tree\own\cats\controllers;

class Main extends \Controller
{
    private $tree;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'TREE' => $this->treeView()
                   ]);

        $this->css();

        return $v;
    }

    private function treeView()
    {
        if ($selectedUser = \ss\models\User::find($this->s('<~:selected_user_id'))) {
            $treeInfo = ss()->own->getTreeInfo($this->tree->id, $selectedUser);

            return $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
                'default'           => [

                ],
                'query_builder'     => $this->_abs('>app:treeQueryBuilder', [
                    'tree_id' => $this->tree->id
                ]),
                'node_control'      => [
                    '>nodeControl:view|',
                    [
                        'cat'                    => '%model',
                        'enabled_ids'            => $treeInfo->enabledIds,
                        'auto_enabled_ids'       => $treeInfo->autoEnabledIds,
                        'has_nested_enabled_ids' => $treeInfo->hasNestedEnabledIds,
                        'merge_ids'              => $treeInfo->mergeIds,
                        'diff_ids'               => $treeInfo->diffIds
                    ]
                ],
                'root_node_id'      => $this->data('root_id'),
                'root_node_visible' => false,
                'expand'            => false
            ]);
        }
    }
}
