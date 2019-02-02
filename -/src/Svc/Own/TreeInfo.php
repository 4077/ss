<?php namespace ss\Svc\Own;

class TreeInfo
{
    public $enabledIds = [];

    public $mergeIds = [];

    public $diffIds = [];

    public $autoEnabledIds = [];

    public $hasNestedEnabledIds = [];

    private $treeInfoBranch = [];

    public function render($treeId, $user)
    {
        $rootCat = ss()->trees->getRootCat($treeId);

        // todo groups

        $userCats = [];

        $user->cats()->get()->each(function ($cat) use (&$userCats) {
            $userCats[$cat->id] = $cat->pivot->mode;
        });

        foreach ($userCats as $catId => $mode) {
            if ($mode == 'MERGE') {
                merge($this->mergeIds, $catId);
                merge($this->enabledIds, $catId);
            }
        }

        $this->treeInfoBranch[] = $rootCat->id;

        $this->renderTreeInfoRecursion(ss()->cats->getTree($treeId), $rootCat);

        foreach ($userCats as $catId => $mode) {
            if ($mode == 'DIFF') {
                merge($this->diffIds, $catId);
                diff($this->enabledIds, $catId);
                diff($this->autoEnabledIds, $catId);
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
