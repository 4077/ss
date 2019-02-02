<?php namespace ss\Svc\Trees;

class CompatibleComponentsTreeInfo
{
    public $enabledIds = [];

    public $mergeIds = [];

    public $diffIds = [];

    public $autoEnabledIds = [];

    public $hasNestedEnabledIds = [];

    public $accessByCatId = [];

    private $treeInfoBranch = [];

    public function render($treeId, $catType, $type)
    {
        $rootCat = components()->cats->getRootCat();

        $pivots = \ss\models\TreeComponentsCat::where('tree_id', $treeId)
            ->where('cat_type', $catType)
            ->where('type', $type)
            ->get();

        foreach ($pivots as $pivot) {
            if ($pivot->mode == 'merge') {
                merge($this->mergeIds, $pivot->cat_id);
                merge($this->enabledIds, $pivot->cat_id);
            }
        }

        $this->renderTreeInfoRecursion(components()->cats->getTree(), $rootCat);

        $appc = appc();

        foreach ($pivots as $pivot) {
            $access = $pivot->access;

            $this->accessByCatId[$pivot->cat_id] = $access;

            if ($pivot->mode == 'diff' || ($access && !$appc->a($access))) {
                merge($this->diffIds, $pivot->cat_id);
                diff($this->enabledIds, $pivot->cat_id);
                diff($this->autoEnabledIds, $pivot->cat_id);
            }
        }

        return $this;
    }

    private function renderTreeInfoRecursion(\ewma\Data\Tree $tree, $node)
    {
        if (array_intersect($this->treeInfoBranch, $this->enabledIds)) {
            merge($this->autoEnabledIds, $node->id);
        }

        if (in_array($node->id, $this->enabledIds)) {
            merge($this->hasNestedEnabledIds, array_slice($this->treeInfoBranch, 0, -1));
        }

        $subnodes = $tree->getSubnodes($node->id);

        foreach ($subnodes as $subnode) {
            $this->treeInfoBranch[] = $subnode->id;

            $this->renderTreeInfoRecursion($tree, $subnode);

            array_pop($this->treeInfoBranch);
        }
    }
}
