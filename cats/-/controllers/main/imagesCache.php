<?php namespace ss\cats\controllers\main;

class ImagesCache extends \Controller
{
    public function resetTreeProducts()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            $builder = $tree->products();

            $builder->update(['images_cache' => '']);

            return 'reset for tree_id=' . $tree->id . ', products count: ' . $builder->count();
        }
    }

    public function resetCatProducts()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $builder = $cat->products();

            $builder->update(['images_cache' => '']);

            return 'reset for cat_id=' . $cat->id . ', products count: ' . $builder->count();
        }
    }

    public function resetCatProductsRecursive()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::getIds($cat);

            $builder = \ss\models\Product::whereIn('cat_id', $catsIds);

            $builder->update(['images_cache' => '']);

            return 'reset for cat_id=' . $cat->id . ', products count: ' . $builder->count();
        }
    }

    public function resetTreeCats()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            $builder = $tree->cats();

            $builder->update(['images_cache' => '']);

            return 'reset for tree_id=' . $tree->id . ', cats count: ' . $builder->count();
        }
    }

    public function resetCat()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $cat->update(['images_cache' => '']);

            return 'reset for cat_id=' . $cat->id;
        }
    }

    public function resetCatRecursive()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::getIds($cat);

            $builder = \ss\models\Cat::whereIn('id', $catsIds);

            $builder->update(['images_cache' => '']);

            return 'reset for cat_id=' . $cat->id . ', cats count: ' . $builder->count();
        }
    }
}
