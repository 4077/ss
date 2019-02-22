<?php namespace ss\cats\controllers;

class Fix extends \Controller
{
    public function resetCatsLess()
    {
        return false;

        \ss\models\Cat::query()->update(['less' => '']);
    }

    public function deleteProductsInTree()
    {
        return false;

        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $ids = \ewma\Data\Tree::getIds($cat);

            return \ss\models\Product::whereIn('cat_id', $ids)->delete();
        }
    }

    public function deleteOrphanedImportChanges()
    {
        return false;

        return \ss\models\ProductsChange::doesntHave('product')->delete();
    }

    public function deleteNestedCatsAndProducts()
    {
        return false;

        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $ids = \ewma\Data\Tree::getIds($cat);

            $output['products deleted'] = \ss\models\Product::whereIn('cat_id', $ids)->delete();

            diff($ids, $this->data('cat_id'));

            $output['cats deleted'] = \ss\models\Cat::whereIn('id', $ids)->delete();

            return $output;
        }
    }

    public function getIds()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            return a2l(\ewma\Data\Tree::getIds($cat));
        }
    }

    public function findRecursion()
    {
        return false;

        $cats = \ss\models\Cat::all();

        $output = [];

        foreach ($cats as $cat) {
            if ($cat->id == $cat->parent_id) {
                $output[] = $cat->id;
            }
        }

        return $output;
    }
}
