<?php namespace ss\cats\controllers\main\info;

class Tree extends \Controller
{
    public function get()
    {
        $tree = $this->data['tree'];

        return [
            'branch' => $this->branch($tree),
            'model'  => $tree->toArray()
        ];
    }

    public function branch($tree)
    {
        $branch = \ewma\Data\Tree::getBranch($tree);

        $output = [];

        foreach ($branch as $node) {
            $output[] = $node->name;
        }

        return $output;
    }
}
