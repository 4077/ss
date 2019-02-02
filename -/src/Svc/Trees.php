<?php namespace ss\Svc;

class Trees extends \ewma\Service\Service
{
    protected $services = ['svc', 'connections', 'plugins'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    /**
     * @var \ss\Svc\Trees\Connections
     */
    public $connections = \ss\Svc\Trees\Connections::class;

    /**
     * @var \ss\Svc\Trees\Plugins
     */
    public $plugins = \ss\Svc\Trees\Plugins::class;

    //
    //
    //

    private $rootNode;

    public function getRootNode()
    {
        if (null === $this->rootNode) {
            $cat = \ss\models\Tree::where('parent_id', 0)->first();

            if (!$cat) {
                $cat = \ss\models\Tree::create(['parent_id' => 0]);
            }

            $this->rootNode = $cat;
        }

        return $this->rootNode;
    }

    private $rootCats;

    public function getRootCat($treeId)
    {
        if (!isset($this->rootCats[$treeId])) {
            $cat = \ss\models\Cat::where('tree_id', $treeId)->where('parent_id', 0)->first();

            if (!$cat) {
                $cat = \ss\models\Cat::create([
                                                  'tree_id'   => $treeId,
                                                  'parent_id' => 0
                                                  // todo сделать автовыбор после замены page>folder
                                              ]);
            }

            $this->rootCats[$treeId] = $cat;
        }

        return $this->rootCats[$treeId];
    }

    private $treesTree;

    /**
     * @return \ewma\Data\Tree
     */
    public function getTree()
    {
        if (empty($this->treesTree)) {
            $this->treesTree = \ewma\Data\Tree::get(\ss\models\Tree::class);
        }

        return $this->treesTree;
    }

    private $treesBranches = [];

    public function getTreeBranch(\ss\models\Tree $tree, $reverse = true)
    {
        if (!isset($this->treesBranches[$tree->id])) {
            $this->treesBranches[$tree->id] = $this->getTree()->getBranch($tree, $reverse);
        }

        return $this->treesBranches[$tree->id];
    }

    public function getNamesBranch(\ss\models\Tree $tree, $reverse = true)
    {
        $branch = $this->getTreeBranch($tree, $reverse);

        $output = [];

        foreach ($branch as $node) {
            $output[$node->id] = $node->name ?: '...';
        }

        return $output;
    }

    public function toggleComponentsCatPivot(\ss\models\Tree $tree, \ewma\components\models\Cat $componentsCat, $catType, $type, $mode)
    {
        $pivot = $this->getComponentsCatPivot($tree, $componentsCat, $catType, $type);

        if ($pivot) {
            $currentMode = $pivot->mode;

            if ($currentMode == $mode) {
                if ($pivot->access) {
                    $pivot->mode = 'none';
                    $pivot->save();
                } else {
                    $pivot->delete();
                }
            } else {
                $pivot->mode = $mode;
                $pivot->save();
            }
        } else {
            \ss\models\TreeComponentsCat::create([
                                                     'tree_id'  => $tree->id,
                                                     'cat_id'   => $componentsCat->id,
                                                     'cat_type' => $catType,
                                                     'type'     => $type,
                                                     'mode'     => $mode
                                                 ]);
        }
    }

    public function setComponentsCatPivotAccess(\ss\models\Tree $tree, \ewma\components\models\Cat $componentsCat, $catType, $type, $access)
    {
        $pivot = $this->getComponentsCatPivot($tree, $componentsCat, $catType, $type);

        if ($pivot) {
            $pivot->access = $access;
            $pivot->save();
        } else {
            \ss\models\TreeComponentsCat::create([
                                                     'tree_id'  => $tree->id,
                                                     'cat_id'   => $componentsCat->id,
                                                     'cat_type' => $catType,
                                                     'mode'     => 'none',
                                                     'access'   => $access
                                                 ]);
        }
    }

    public function getComponentsCatPivot(\ss\models\Tree $tree, \ewma\components\models\Cat $componentsCat, $catType, $type)
    {
        return \ss\models\TreeComponentsCat::where('tree_id', $tree->id)
            ->where('cat_id', $componentsCat->id)
            ->where('cat_type', $catType)
            ->where('type', $type)
            ->first();
    }

    private $availableComponentsCatsIds;

    public function getAvailableComponentsCatsIds($treeId, $catType, $type)
    {
        if (!isset($this->availableComponentsCatsIds[$treeId][$catType][$type])) {
            $treeInfo = $this->getCompatibleComponentsTreeInfo($treeId, $catType, $type);

            $this->availableComponentsCatsIds[$treeId][$catType][$type] = [];

            merge($this->availableComponentsCatsIds[$treeId][$catType][$type], $treeInfo->enabledIds);
            merge($this->availableComponentsCatsIds[$treeId][$catType][$type], $treeInfo->autoEnabledIds);
        }

        return $this->availableComponentsCatsIds[$treeId][$catType][$type];
    }

    private $treeInfo;

    public function getCompatibleComponentsTreeInfo($treeId, $catType, $type)
    {
        $cacheIndex = $treeId . '/' . $catType;

        if (!isset($this->treeInfo[$cacheIndex])) {
            $this->treeInfo[$cacheIndex] = (new \ss\Svc\Trees\CompatibleComponentsTreeInfo)->render($treeId, $catType, $type);
        }

        return $this->treeInfo[$cacheIndex];
    }

    public function duplicate(\ss\models\Tree $tree)
    {
        $newTree = \ss\models\Tree::create(unmap($tree->toArray(), 'id'));

        $componentsCats = \ss\models\TreeComponentsCat::where('tree_id', $tree->id)->get();

        foreach ($componentsCats as $componentsCatPivot) {
            $componentsCatPivotData = unmap($componentsCatPivot->toArray(), 'id');

            $componentsCatPivotData['tree_id'] = $newTree->id;

            \ss\models\TreeComponentsCat::create($componentsCatPivotData);
        }

        return $newTree;
    }

    public function updateSearchIndex(\ss\models\Tree $tree)
    {
        $products = $tree->products()->get();

        foreach ($products as $product) {
            $this->svc->products->updateSearchIndex($product);
        }

        return count($products);
    }

    public function updateMultisourceCache(\ss\models\Tree $tree, $sleep = 100)
    {
        $usleep = $sleep * 1000;

        $products = $tree->products()->get();

        $count = count($products);

        $cli = app()->mode === \Ewma\App\App::REQUEST_MODE_CLI;

        foreach ($products as $n => $product) {
            $this->svc->products->updateMultisourceCache($product);

            if ($cli) {
                print $n . '/' . $count . PHP_EOL;
            }

            usleep($usleep);
        }
    }
}
