<?php namespace ss\controllers\main;

class Multisource extends \Controller
{
    public function updateTreeCache()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            $products = $tree->products;

            $count = 0;

            foreach ($products as $n => $product) {
                ss()->products->updateMultisourceCache($product);

                $this->log($n . '/' . $count);
            }
        }
    }

    public function updateProductCache()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            ss()->products->updateMultisourceCache($product);
        }
    }

    public function getInstance()
    {
        return ss()->products->getMultisourceInstance($this->data('stock_warehouses'), $this->data('under_order_warehouses'));
    }

    public function getProductCache()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            return _j($product->multisource_cache);
        }
    }

    public function clearCatMultisourceSummary()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::getIds($cat);

            $products = \ss\models\Product::whereIn('cat_id', $catsIds)->get();

            ss()->products->clearMultisourceSummary(table_ids($products));
        }
    }
}
