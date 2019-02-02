<?php namespace ss\cats\controllers\main;

class Components extends \Controller
{
    /**
     * присоединение компонентов ко всем категориям дерева
     *
     */
    public function attach()
    {
        $treeId = $this->data('tree_id');
        $catType = $this->data('cat_type');
        $type = $this->data('type');
        $componentId = $this->data('component_id');

        $rootCat = ss()->trees->getRootCat($treeId);

        $tree = \ewma\Data\Tree::get(\ss\models\Cat::where('type', $catType)->where('tree_id', $treeId));

        $flatten = $tree->getFlattenData($rootCat->id);

        foreach ($flatten['nodes_by_id'] as $id => $cat) {
            $attached = \ss\models\CatComponent::with(['cat', 'component'])
                ->where('cat_id', $cat->id)
                ->where('component_id', $componentId)
                ->where('type', $type)
                ->get();

            if (!count($attached)) {
                $defaultData = [];

                if ($component = $this->getComponentById($componentId)) {
                    if ($defaultDataHandler = components()->getHandler($component, 'default-data')) {
                        $defaultData = handlers()->render($defaultDataHandler);
                    }

                    \ss\models\CatComponent::create([
                                                        'cat_id'       => $cat->id,
                                                        'component_id' => $componentId,
                                                        'type'         => $type,
                                                        'data'         => j_($defaultData)
                                                    ]);
                } else {
                    $this->console('component with id=' . $componentId . ' not exists');
                }
            }
        }
    }

    /**
     * обновление полей для всех категорий дерева
     */
    public function update()
    {
        $treeId = $this->data('tree_id');
        $catType = $this->data('cat_type');
        $type = $this->data('type');
        $componentId = $this->data('component_id');
        $update = $this->data('update');

        $rootCat = ss()->trees->getRootCat($treeId);

        $tree = \ewma\Data\Tree::get(\ss\models\Cat::where('type', $catType)->where('tree_id', $treeId));

        $flatten = $tree->getFlattenData($rootCat->id);

        foreach ($flatten['nodes_by_id'] as $id => $cat) {
            $attached = \ss\models\CatComponent::with(['cat', 'component'])
                ->where('cat_id', $cat->id)
                ->where('component_id', $componentId)
                ->where('type', $type)
                ->get();

            foreach ($attached as $pivot) {
                foreach ($update as $field => $value) {
                    $pivot->{$field} = $field == 'data' ? j_($value) : $value;
                }

                $pivot->save();
            }
        }
    }

    /**
     * обновление поля data для всех категорий дерева
     */
    public function updateTreeData()
    {
        $treeId = $this->data('tree_id');
        $catType = $this->data('cat_type');
        $type = $this->data('type');
        $componentId = $this->data('component_id');
        $data = $this->data('data');
        $mode = (int)$this->data('mode');

        $rootCat = ss()->trees->getRootCat($treeId);

        $tree = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $treeId));

        $flatten = $tree->getFlattenData($rootCat->id);

        $count = 0;

        foreach ($flatten['nodes_by_id'] as $id => $cat) {
            if ($cat->type == $catType) {
                $attached = \ss\models\CatComponent::with(['cat', 'component'])
                    ->where('cat_id', $cat->id)
                    ->where('component_id', $componentId)
                    ->where('type', $type)
                    ->get();

                foreach ($attached as $pivot) {
                    $pivotData = _j($pivot->data);

                    if ($mode == AA) {
                        aa($pivotData, $data);
                    }

                    if ($mode == RA) {
                        ra($pivotData, $data);
                    }

                    if ($mode == RR) {
                        $pivotData = $data;
                    }

                    $pivot->data = j_($pivotData);
                    $pivot->save();

                    $this->log('update pivot: ' . $pivot->id);

                    $count++;
                }
            }
        }

        $this->log('updated pivots: ' . $count);
    }

    /**
     * обновление поля data для категории и всех подкатегорий
     */
    public function updateSubtreeData()
    {
        $catId = $this->data('cat_id');
        $catType = $this->data('cat_type');
        $type = $this->data('type');
        $componentId = $this->data('component_id');
        $data = $this->data('data');
        $mode = (int)$this->data('mode');

        if ($cat = \ss\models\Cat::find($catId)) {
            $tree = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $cat->tree_id));

            $flatten = $tree->getFlattenData($cat->id);

            $count = 0;

            foreach ($flatten['nodes_by_id'] as $id => $cat) {
                if ($cat->type == $catType) {
                    $attached = \ss\models\CatComponent::with(['cat', 'component'])
                        ->where('cat_id', $cat->id)
                        ->where('component_id', $componentId)
                        ->where('type', $type)
                        ->get();

                    foreach ($attached as $pivot) {
                        $pivotData = _j($pivot->data);

                        if ($mode == AA) {
                            aa($pivotData, $data);
                        }

                        if ($mode == RA) {
                            ra($pivotData, $data);
                        }

                        if ($mode == RR) {
                            $pivotData = $data;
                        }

                        $pivot->data = j_($pivotData);
                        $pivot->save();

                        $this->log('update pivot: ' . $pivot->id);

                        $count++;
                    }
                }
            }

            $this->log('updated pivots: ' . $count);
        }
    }

    /**
     * накатывание нового дефолта на все связи категорий с компонентами
     */
    public function aaAllPivotsDefaultData()
    {
        $cats = \ss\models\Cat::all();

        $catsCount = 0;
        $pivotsCount = 0;
        $componentsCount = 0;
        $updatedCount = 0;

        foreach ($cats as $cat) {
            $attached = \ss\models\CatComponent::with(['cat', 'component'])
                ->where('cat_id', $cat->id)
                ->get();

            foreach ($attached as $pivot) {
                if ($component = $pivot->component) {
                    if ($defaultDataHandler = components()->getHandler($component, 'default-data')) {
                        $defaultData = handlers()->render($defaultDataHandler);

                        $pivotData = _j($pivot->data);

                        $md5 = jmd5($pivotData);

                        aa($pivotData, $defaultData);

                        if (jmd5($pivotData) != $md5) {
                            $pivot->data = j_($pivotData);
                            $pivot->save();

                            $this->log('update pivot: ' . $pivot->id);

                            $updatedCount++;
                        } else {
                            $this->log('skip pivot: ' . $pivot->id);
                        }
                    }

                    $componentsCount++;
                }

                $pivotsCount++;
            }

            $catsCount++;
        }

        $this->log('updated pivots: ' . $updatedCount . ', cats: ' . $catsCount . ', pivots: ' . $pivotsCount . ', components: ' . $componentsCount);
    }

    private $componentsById = [];

    private function getComponentById($componentId)
    {
        if (!isset($this->componentsById[$componentId])) {
            $this->componentsById[$componentId] = \ewma\components\models\Component::find($componentId);
        }

        return $this->componentsById[$componentId];
    }
}
