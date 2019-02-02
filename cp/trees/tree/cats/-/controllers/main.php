<?php namespace ss\cp\trees\tree\cats\controllers;

class Main extends \Controller
{
    private $s;

    private $tree;

    private $cat;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();

        $this->s = $this->s('|', [
            'selected_id' => false
        ]);

        $this->cat = \ss\models\Cat::find($this->s['selected_id']);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $cat = $this->cat;

        $v->assign([
                       'CONTENT' => $this->catsTreeView(),
                       'CAT'     => $this->catView()
                   ]);

        $this->css();

        $this->widget(':|', [
            '.e' => [
                'ss/tree/' . $this->tree->id . '/update_pages'      => 'mr.reload',
                'ss/tree/' . $this->tree->id . '/update_containers' => 'mr.reload'
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload', [
                    'tree' => xpack_model($this->tree)
                ])
            ]
        ]);

        return $v;
    }

    private function catView()
    {
        if ($cat = $this->cat) {
            // todo folder

            if ($cat->type == 'page') {
                return $this->c('\ss\cats\cp\page~:view|ss/trees', [
                    'cat' => $cat
                ]);
            }

            if ($cat->type == 'container') {
                return $this->c('\ss\cats\cp\container~:view|ss/trees', [
                    'cat' => $cat
                ]);
            }
        }
    }

    private function catsTreeView()
    {
        $rootNode = ss()->trees->getRootCat($this->tree->id);

        return $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
            'default'          => [

            ],
            'node_control'     => [
                '>node:view|',
                [
                    'root_node_id' => $rootNode->id,
                    'tree'         => pack_model($this->tree),
                    'cat'          => '%model'
                ]
            ],
            'query_builder'    => $this->_abs('>app:treeQueryBuilder|', [
                'tree_id' => $this->tree->id
            ]),
            'root_node_id'     => $rootNode->id,
            'expand'           => false,
            'sortable'         => false,
            'movable'          => false,
            'selected_node_id' => $this->s['selected_id']
        ]);
    }
}
