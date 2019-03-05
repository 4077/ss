<?php namespace ss\multisource\controllers;

class Main extends \Controller
{
    public function updateTreeProductsSummary()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            $products = $tree->products;

            $count = count($products);

            foreach ($products as $n => $product) {
                ss()->multisource->updateSummary($product);

                $this->log(($n + 1) . '/' . $count);
            }
        }
    }

    public function updateCatProductsSummaryRecursive()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $ids = \ewma\Data\Tree::getIds($cat);

            $products = \ss\models\Product::whereIn('cat_id', $ids)->get();

            $count = count($products);

            foreach ($products as $n => $product) {
                ss()->multisource->updateSummary($product);

                $this->log(($n + 1) . '/' . $count);
            }
        }
    }

    public function updateProductSummary()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            ss()->multisource->updateSummary($product);
        }
    }

    public function updateProductData()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            ss()->products->updateMultisourceData($product, $this->data);
        }
    }

    public function getProductCache()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            return _j($product->multisource_cache);
        }
    }

    public function getProductSummary()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            return ss()->multisource->getSummary($product);
        }
    }

    public function getProductCacheAndSummary()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            return [
                'cache'   => $this->getProductCache(),
                'summary' => $this->getProductSummary()
            ];
        }
    }
}
