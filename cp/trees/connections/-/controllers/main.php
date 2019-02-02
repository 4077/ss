<?php namespace ss\cp\trees\connections\controllers;

class Main extends \Controller
{
    private $s;

    private $tree;

    public function __create()
    {
        $this->s = &$this->s('|', [
            'selected_tree_id'      => false,
            'selected_connection'   => [
                'source_tree_id' => false,
                'target_tree_id' => false
            ],
            'tab'                   => false,
            'selected_adapter_name' => false
        ]);

        $s = &$this->s;

        if ($selectTreeId = $this->data('select_tree_id')) {
            $s['selected_tree_id'] = $selectTreeId;
        }

        if (!$s['selected_tree_id']) {
            if ($firstTree = \ss\models\TreesConnection::where('instance', $this->_instance())->first()) {
                $s['selected_tree_id'] = $firstTree->source_id;
            }
        }

        $this->tree = \ss\models\Tree::find($s['selected_tree_id']);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');
        $s = $this->s('|');

        $selectedTree = $this->tree;

        if ($selectedTree) {
            $targets = \ss\models\TreesConnection::where('instance', $this->_instance())->where('source_id', $selectedTree->id)->get();
            $sources = \ss\models\TreesConnection::where('instance', $this->_instance())->where('target_id', $selectedTree->id)->get();

            $allConnections = \ss\models\TreesConnection::where('instance', $this->_instance())->get();

            $allTargets = table_cells($allConnections, 'target_id');
            $allSources = table_cells($allConnections, 'source_id');

            $allIds = [];

            merge($allIds, $allTargets);
            merge($allIds, $allSources);

            $allTrees = \ss\models\Tree::whereIn('id', $allIds)->get();

            foreach ($allTrees as $tree) {
                $v->assign('tree', [
                    'BUTTON' => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:select|',
                        'data'  => [
                            'tree' => xpack_model($tree)
                        ],
                        'class' => 'select_tree_button ' . ($tree->mode),
                        'label' => implode('/', array_slice(ss()->trees->getNamesBranch($tree, false), 1)),
                        'icon'  => 'fa fa-' . ($tree->mode == 'folders' ? 'folder' : 'file')
                    ]),
                ]);
            }

            foreach ($targets as $target) {
                $tree = $target->target;

                $v->assign('target', [
                    'SELECT_CONNECTION_BUTTON' => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:selectConnection|',
                        'data'  => [
                            'tree' => xpack_model($tree)
                        ],
                        'class' => 'select_connection_button',
                        'label' => implode('/', array_slice(ss()->trees->getNamesBranch($tree, false), 1)),
                        'icon'  => 'fa fa-' . ($tree->mode == 'folders' ? 'folder' : 'file')
                    ]),
                    'SELECT_BUTTON'            => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:select|',
                        'data'  => [
                            'tree' => xpack_model($tree)
                        ],
                        'class' => 'select_button',
                        'icon'  => 'fa fa-arrow-left'
                    ])
                ]);
            }

            foreach ($sources as $source) {
                $tree = $source->source;

                $v->assign('source', [
                    'SELECT_CONNECTION_BUTTON' => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:selectConnection|',
                        'data'  => [
                            'tree' => xpack_model($tree)
                        ],
                        'class' => 'select_connection_button',
                        'label' => implode('/', array_slice(ss()->trees->getNamesBranch($tree, false), 1)),
                        'icon'  => 'fa fa-' . ($tree->mode == 'folders' ? 'folder' : 'file')
                    ]),
                    'SELECT_BUTTON'            => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:select|',
                        'data'  => [
                            'tree' => xpack_model($tree)
                        ],
                        'class' => 'select_button',
                        'icon'  => 'fa fa-arrow-right'
                    ])
                ]);
            }

            $v->assign([
                           'NAME'          => implode('/', array_slice(ss()->trees->getNamesBranch($selectedTree, false), 1)),
                           'SELECTED_ICON' => ($selectedTree->mode == 'folders' ? 'folder' : 'file')
                       ]);

            $adaptersComponentsCatId = ss()->config('trees/connections/adapters/components_cat_id');

            $adaptersComponents = \ewma\components\models\Component::where('cat_id', $adaptersComponentsCatId)->orderBy('position')->get();

            foreach ($adaptersComponents as $adapterComponent) {
                $adapterName = $adapterComponent->name;

                if ($adapterDataHandler = components()->getHandler($adapterComponent, 'data')) {
                    $adapterData = handlers()->render($adapterDataHandler);

                    $isSelected = $s['selected_adapter_name'] == $adapterName;

                    $v->assign('tab', [
                        'BUTTON' => $this->c('\std\ui button:view', [
                            'path'    => '>xhr:selectAdapter|',
                            'data'    => [
                                'name' => $adapterName
                            ],
                            'class'   => 'tab ' . ($isSelected ? 'selected' : ''),
                            'content' => $adapterData['name']
                        ])
                    ]);

                    if ($isSelected) {
                        list($source, $target) = $this->getSelectedConnectionsTrees();

                        if ($source && $target) {
                            if ($connection = ss()->trees->connections->get($source, $target)) {
                                $stHandler = components()->getHandler($adapterComponent, 'st');
                                $tsHandler = components()->getHandler($adapterComponent, 'ts');

                                $v->assign([
                                               'SOURCE'     => a2p(array_slice(ss()->trees->getNamesBranch($source, false), 1)),
                                               'TARGET'     => a2p(array_slice(ss()->trees->getNamesBranch($target, false), 1)),
                                               'ST_CONTENT' => $stHandler ? handlers()->render($stHandler, [
                                                   'connection' => $connection,
                                                   'adapter'    => $adapterName,
                                                   'instance'   => path($connection->id, $adapterName, 'st'),
                                                   'direction'  => 'st'
                                               ]) : '',
                                               'TS_CONTENT' => $tsHandler ? handlers()->render($tsHandler, [
                                                   'connection' => $connection,
                                                   'adapter'    => $adapterName,
                                                   'instance'   => path($connection->id, $adapterName, 'ts'),
                                                   'direction'  => 'ts'
                                               ]) : ''
                                           ]);
                            }
                        }
                    }
                }
            }
        }

        $this->css();

        $this->widget(':|');

        return $v;
    }

    private function getSelectedConnectionsTrees()
    {
        $source = \ss\models\Tree::find($this->s['selected_connection']['source_tree_id']);
        $target = \ss\models\Tree::find($this->s['selected_connection']['target_tree_id']);

        if ($source || $target) {
            return [$source ?? false, $target ?? false];
        }
    }
}
