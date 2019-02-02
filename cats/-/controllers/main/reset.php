<?php namespace ss\cats\controllers\main;

class Reset extends \Controller
{
    public function productsImagesCache()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::getIds($cat);

            \ss\models\Product::whereIn('cat_id', $catsIds)->update(['images_cache' => '']);
        }
    }
}
