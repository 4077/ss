<?php namespace ss\Svc;

class Cats extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    public function getName(\ss\models\Cat $cat)
    {
        return $cat->name ?: ($cat->short_name ?: '');
    }

    public function getShortName(\ss\models\Cat $cat)
    {
        return $cat->short_name ?: ($cat->name ?: '');
    }

    public function createFolder(\ss\models\Cat $cat, $data = [])
    {
        if ($cat->type == 'page') { // todo page>folder
            $newCatData = ['tree_id' => $cat->tree_id];

            aa($newCatData, $data);

            $newCatData['type'] = 'page'; // todo page>folder

            $newCat = $cat->nested()->create($newCatData);

            return $newCat;
        }
    }

    public function createPage(\ss\models\Cat $cat, $data = [])
    {
        if ($cat->type == 'page') {
            $newCatData = ['tree_id' => $cat->tree_id];

            aa($newCatData, $data);

            $newCatData['type'] = 'page';

            $newCat = $cat->nested()->create($newCatData);

            return $newCat;
        }

        if ($cat->type == 'container') {
            $newCatData = ['tree_id' => $cat->tree_id];

            aa($newCatData, $data);

            $newCatData['type'] = 'page';
            $newCatData['container_id'] = $cat->id;

            $newCat = $cat->parent->nested()->create($newCatData);

            return $newCat;
        }
    }

    public function createContainer(\ss\models\Cat $cat, $data = [])
    {
        if ($cat->type == 'page') {
            $newCatData = ['tree_id' => $cat->tree_id];

            aa($newCatData, $data);

            $newCatData['type'] = 'container';

            $newCat = $cat->nested()->create($newCatData);

            return $newCat;
        }
    }

    public function copyTo(\ss\models\Cat $cat, \ss\models\Cat $targetCat)
    {
        $newCatData = unmap($cat->toArray(), 'id');

        ra($newCatData, [
            'tree_id' => $targetCat->tree_id
        ]);

        if ($cat->type == 'page') {
            if ($targetCat->type == 'container') {
                $newCatData['container_id'] = $targetCat->id;

                $targetCat = $targetCat->parent;
            } elseif ($targetCat->type == 'page') {
                $newCatData['container_id'] = false;
            }
        }

        $newCat = $targetCat->nested()->create($newCatData);

        // components

        $pivots = ss()->cats->getComponentsPivots($cat);

        foreach ($pivots as $pivot) {
            $newPivotData = unmap($pivot->toArray(), 'cat, component, id');
            $newPivotData['cat_id'] = $newCat->id;

            \ss\models\CatComponent::create($newPivotData);
        }

        // images

        appc('\std\images~:copy', [
            'source' => $cat,
            'target' => $newCat
        ]);

        // less

        $modulePath = 'customNodes/ss/cats/less';

        $lessTypes = $this->getLessTypes($cat->type);

        foreach ($lessTypes as $lessType) {
            $sourceNode = $modulePath . ' cat_' . $cat->id . '/' . $lessType;
            $targetNode = $modulePath . ' cat_' . $newCat->id . '/' . $lessType;

            $sourceFilePath = abs_path(appc()->_nodeFilePath($sourceNode, 'less') . '.less');
            $targetFilePath = abs_path(appc()->_nodeFilePath($targetNode, 'less') . '.less');

            if (file_exists($sourceFilePath)) {
                mdir(dirname($targetFilePath));

                copy($sourceFilePath, $targetFilePath);
            }
        }

        //
        // todo сделать чтобы компоненты могли сообщать о том какие сущности в себе несут, чтобы их тоже можно было скопировать. копирование сущностей должно быть опциональным
    }

    private $rootCats;

    public function getRootCat(\ss\models\Cat $cat)
    {
        if (!isset($this->rootCats[$cat->tree_id])) {
            $cat = \ss\models\Cat::where('tree_id', $cat->tree_id)->where('parent_id', 0)->first();

            $this->rootCats[$cat->tree_id] = $cat;
        }

        return $this->rootCats[$cat->tree_id];
    }

    private $catsTree;

    /**
     * @param $treeId
     *
     * @return \ewma\Data\Tree
     */
    public function getTree($treeId)
    {
        if (empty($this->catsTree[$treeId])) {
            $this->catsTree[$treeId] = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $treeId));
        }

        return $this->catsTree[$treeId];
    }

    private $catsBranches = [];

    public function getCatBranch(\ss\models\Cat $cat, $reverse = true)
    {
        if (!isset($this->catsBranches[$cat->id])) {
            $this->catsBranches[$cat->id] = $this->getTree($cat->tree_id)->getBranch($cat, $reverse);
        }

        return $this->catsBranches[$cat->id];
    }

    public function getNamesBranch(\ss\models\Cat $cat, $reverse = true)
    {
        $branch = $this->getCatBranch($cat, $reverse);

        $output = [];

        foreach ($branch as $node) {
            $output[$node->id] = $this->getName($node) ?: '...';
        }

        return $output;
    }

    public function updateRouteCache(\ss\models\Cat $cat)
    {
        $segments = [$cat->alias];

        $parentCat = $cat;
        while ($parentCat = $parentCat->parent) {
            if ($parentCat->alias) {
                $segments[] = $parentCat->alias;
            }
        }

        $route = '';
        if ($segments) {
            $segments = array_reverse($segments);
            $route = implode('/', $segments);
        }

        $cat->route_cache = $route;
        $cat->save();

        foreach ($cat->nested as $nestedCat) {
            $this->updateRouteCache($nestedCat);
        }
    }

    public function render(\ss\models\Cat $cat, $instance = '', $ra = []) // deprecate
    {
        $catComponentsPivots = $this->getEnabledComponentsPivots($cat);

        $output = '';

        foreach ($catComponentsPivots as $pivot) {
            $output .= $this->renderComponentPivot($pivot, $instance, $ra);
        }

        // подумать чё с ним делать
        if ($cat->handler_enabled && $handler = $cat->handlers()->first()) {
            $output = handlers()->render($handler, [
                'cat'     => $cat,
                'content' => $output
            ]);
        }

        return $output;
    }

    public function renderComponentPivot(\ss\models\CatComponent $pivot, $instance = '', $ra = [])
    {
        $component = $pivot->component;
        $cat = $pivot->cat;

        $handler = components()->getHandler($component, $instance);

        /*
        поле не существует пока, оставлю в качестве задумки

        $data = _j($cat->data);

        ra($data, _j($pivot->data));
        */

        $data = _j($pivot->data);

        $data['pivot'] = $pivot;
        $data['cat'] = $cat;

        if ($ra) {
            ra($data, $ra);
        }

        $output = handlers()->render($handler, $data);

        if ($output instanceof \ewma\Views\View) {
            $output = $output->render();
        }

        return $output;
    }

    public function invertComponentPivotData(\ss\models\CatComponent $pivot, $path)
    {
        $value = $this->apComponentPivotData($pivot, $path);

        $this->apComponentPivotData($pivot, $path, !$value);
    }

    public function apComponentPivotData(\ss\models\CatComponent $pivot, $path, $value = null)
    {
        $data = _j($pivot->data);

        if (null !== $value) {
            ap($data, $path, $value);

            $pivot->data = j_($data);
            $pivot->save();
        } else {
            return ap($data, $path);
        }
    }

    public function getComponentPivot(\ss\models\Cat $cat, \ewma\components\models\Component $component)
    {
        return \ss\models\CatComponent::where('cat_id', $cat->id)->where('component_id', $component->id)->first();
    }

    public function getComponentsPivots(\ss\models\Cat $cat, $type = false)
    {
        $builder = \ss\models\CatComponent::with(['cat', 'component'])->where('cat_id', $cat->id);

        if ($type) {
            $builder = $builder->where('type', $type);
        }

        return $builder->orderBy('position')->get();
    }

    public function getEnabledComponentsPivots(\ss\models\Cat $cat) // deprecate
    {
        return \ss\models\CatComponent::with(['cat', 'component'])->where('cat_id', $cat->id)->where('enabled', true)->orderBy('position')->get();
    }

    public function getEnabledRenderers(\ss\models\Cat $cat)
    {
        return \ss\models\CatComponent::with(['cat', 'component'])
            ->where('cat_id', $cat->id)
            ->where('enabled', true)
            ->where('type', 'renderer')
            ->orderBy('position')
            ->get();
    }

    public function getEnabledWrappers(\ss\models\Cat $cat)
    {
        return \ss\models\CatComponent::with(['cat', 'component'])
            ->where('cat_id', $cat->id)
            ->where('enabled', true)
            ->where('type', 'wrapper')
            ->orderBy('position', 'DESC')
            ->get();
    }

    public function getPinnedComponentsPivots(\ss\models\Cat $cat)
    {
        return \ss\models\CatComponent::with(['cat', 'component'])->where('cat_id', $cat->id)->where('pinned', true)->orderBy('position')->get();
    }

    /**
     * @param \ss\models\Cat $cat
     *
     * @return \ss\models\CatComponent
     */
    public function getFirstEnabledComponentPivot(\ss\models\Cat $cat)
    {
        return \ss\models\CatComponent::with(['cat', 'component'])->where('cat_id', $cat->id)->where('enabled', true)->orderBy('position')->first();
    }

    public function setEnabledComponent(\ss\models\Cat $cat, \ewma\components\models\Component $component) // deprecate
    {
        \ss\models\CatComponent::where('cat_id', $cat->id)->update(['enabled' => false]);

        $defaultData = [];
        if ($defaultDataHandler = components()->getHandler($component, 'default-data')) {
            $defaultData = handlers()->render($defaultDataHandler);
        }

        if ($pivot = \ss\models\CatComponent::where('cat_id', $cat->id)->where('component_id', $component->id)->first()) {
            $pivotData = _j($pivot->data);
            aa($pivotData, $defaultData);

            $pivot->enabled = true;
            $pivot->data = j_($pivotData);
            $pivot->save();
        } else {
            \ss\models\CatComponent::create([
                                                'cat_id'       => $cat->id,
                                                'component_id' => $component->id,
                                                'enabled'      => true,
                                                'data'         => j_($defaultData)
                                            ]);
        }
    }

    public function getHandler(\ss\models\Cat $cat, $instance = '')
    {
        return $cat->handler($instance);
    }

    public function getOrCreateHandler(\ss\models\Cat $cat, $instance = '')
    {
        if (!$handler = $this->getHandler($cat, $instance)) {
            $handler = $this->createHandler($cat, $instance);
        }

        return $handler;
    }

    public function createHandler(\ss\models\Cat $cat, $instance = '')
    {
        return $cat->handlers()->create(['instance' => $instance]);
    }

    public function resetImagesCache(\ss\models\Cat $cat, $inSubtree = false) // todo test
    {
        if ($inSubtree) {
            $catsIds = \ewma\Data\Tree::getIds(\ss\models\Cat::where('tree_id', $cat->tree_id));
        } else {
            $catsIds = [$cat->id];
        }

        \ss\models\Cat::whereIn('id', $catsIds)->update(['images_cache' => '']);
    }

    public function resetProductsImagesCache(\ss\models\Cat $cat, $inSubtree = false) // todo test
    {
        if ($inSubtree) {
            $catsIds = \ewma\Data\Tree::getIds(\ss\models\Cat::where('tree_id', $cat->tree_id));
        } else {
            $catsIds = [$cat->id];
        }

        \ss\models\Product::whereIn('cat_id', $catsIds)->update(['images_cache' => '']);
    }

    public function isEditable(\ss\models\Cat $cat)
    {
        $ssc = ssc();

        return $ssc->a('cats/edit') || ($ssc->a('cats/edit/own') && $this->svc->own->isCatOwn($cat->tree_id, $cat));
    }

    public function isCDable(\ss\models\Cat $cat)
    {
        $ssc = ssc();

        return $ssc->a('cats/cd') || ($ssc->a('cats/cd/own') && $this->svc->own->isCatOwn($cat->tree_id, $cat));
    }

    public function isProductsEditable(\ss\models\Cat $cat)
    {
        $ssc = ssc();

        return $ssc->a('products/edit') || ($ssc->a('products/edit/in_own_cats') && $this->svc->own->isCatOwn($cat->tree_id, $cat->id));
    }

    public function isProductsCDable(\ss\models\Cat $cat)
    {
        $ssc = ssc();

        return $ssc->a('products/cd') || ($ssc->a('products/cd/in_own_cats') && $this->svc->own->isCatOwn($cat->tree_id, $cat->id));
    }

    public function getDefaultLess($catType)
    {
        if ($catType == 'page') {
            return [
                'global' => [
                    'inheritable' => true,
                    'enabled'     => false,
                    'rewrite'     => false
                ],
                'layout' => [
                    'inheritable' => true,
                    'enabled'     => false,
                    'rewrite'     => false
                ],
                'cat'    => [
                    'inheritable' => true,
                    'enabled'     => false,
                    'rewrite'     => false
                ]
            ];
        }

        if ($catType == 'container') {
            return [
                'container' => [
                    'inheritable' => false,
                    'enabled'     => false
                ]
            ];
        }
    }

    public function getLessTypes($catType)
    {
        $lessTypes = [
            'folder'    => ['global', 'layout', 'cat'], // todo page>folder
            'page'      => ['global', 'layout', 'cat'],
            'container' => ['container']
        ];

        return $lessTypes[$catType];
    }

    public function getLess(\ss\models\Cat $cat)
    {
        $less = _j($cat->less);

        if (!$less) {
            $less = $this->getDefaultLess($cat->type);

            $cat->less = j_($less);
            $cat->save();
        }

        return $less;
    }

    public function getLessNodes(\ss\models\Cat $cat)
    {
        $merged = [];

        $branch = $this->getCatBranch($cat, false);

        foreach ($branch as $cat) {
            $catLess = $this->getLess($cat);

            foreach ($catLess as $type => $data) {
                if ($data['enabled']) {
                    if ($data['inheritable'] && $data['rewrite']) {
                        $merged[$type] = [];
                    }

                    $merged[$type][] = '\customNodes\ss\cats\less cat_' . $cat->id . '/' . $type;
                }
            }
        }

        $flat = a2f($merged);

        $output = [];
        foreach ($flat as $nodePath) {
            if ($nodePath) {
                $output[] = $nodePath;
            }
        }

        return $output;
    }

    public function export()
    {

    }

    public function getDeleteInfo(\ss\models\Cat $cat)
    {
        $deleteInfo = new \ss\Svc\Cats\DeleteInfo($cat);

        return $deleteInfo->render();
    }

    public function updateSearchIndex(\ss\models\Cat $cat)
    {
        $products = $cat->products;

        foreach ($products as $product) {
            $this->svc->products->updateSearchIndex($product);
        }
    }
}
