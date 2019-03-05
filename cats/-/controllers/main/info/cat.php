<?php namespace ss\cats\controllers\main\info;

class Cat extends \Controller
{
    public function get()
    {
        $cat = $this->data['cat'];

        return [
            'branch' => $this->branch($cat),
            'model'  => $cat->toArray(),
            'tree'   => $cat->tree
                ? $this->c('@tree:get', [
                    'tree' => $cat->tree
                ])
                : '-'
        ];
    }

    private function branch($cat)
    {
        $branch = \ewma\Data\Tree::getBranch($cat);

        $output = [];

        foreach ($branch as $node) {
            $output[] = ss()->cats->getName($node);
        }

        return $output;
    }
}
