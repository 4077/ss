<?php namespace ss\cats\controllers\main;

class Info extends \Controller
{
    public function product()
    {
        if ($productId = $this->data('product_id')) {
            if ($product = \ss\models\Product::find($productId)) {
                return $this->c('>product:get', [
                    'product' => $product
                ]);
            }
        }

        if ($code = $this->data('code')) {
            $products = \ss\models\Product::where('articul', $code)->orWhere('remote_articul', $code)->orWhere('vendor_code', $code)->get();

            $output = [];

            foreach ($products as $product) {
                $output[$product->id] = $this->c('>product:get', [
                    'product' => $product
                ]);
            }

            return $output;
        }
    }

    public function cat()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            return $this->c('>cat:get', [
                'cat' => $cat
            ]);
        }
    }

    public function tree()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            return $this->c('>tree:get', [
                'tree' => $tree
            ]);
        }
    }

    public function productHistory()
    {
        if ($productId = $this->data('product_id')) {
            $product = \ss\models\Product::find($productId);
            $warehouse = \ss\multisource\models\Warehouse::find($this->data('warehouse_id'));

            if ($product && $warehouse) {
                return $this->c('>product:history', [
                    'product'   => $product,
                    'warehouse' => $warehouse
                ]);
            }
        }
    }
}
