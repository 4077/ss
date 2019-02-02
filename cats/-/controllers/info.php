<?php namespace ss\cats\controllers;

class Info extends \Controller
{
    public function branch()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $branch = \ewma\Data\Tree::getBranch($cat);

            $output = [];

            foreach ($branch as $node) {
                $output[] = ss()->cats->getName($node);
            }

            return $output;
        }
    }

    public function countProducts()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::getIds($cat);

            return \ss\models\Product::whereIn('cat_id', $catsIds)->count();
        }
    }
}
