<?php namespace ss\controllers\main;

class Info extends \Controller
{
    public function product()
    {
        $output = [];

        if ($productId = $this->data('product_id')) {
            $product = \ss\models\Product::find($productId);

            $output[] = [
                'cat'     => $this->catBranch($product->cat_id),
                'product' => $product->toArray()
            ];
        }

        if ($code = $this->data('code')) {
            $products = \ss\models\Product::where('articul', $code)->orWhere('remote_articul', $code)->orWhere('vendor_code', $code)->get();

            foreach ($products as $product) {
                $productInfo = map($product->toArray(), 'id, articul, , vendor_code, name, short_name');

                $productInfo['multisource_cache'] = _j($product->multisource_cache);

                $output[] = [
                    'tree'    => a2p($this->treeBranch($product->tree_id)),
                    'cat'     => a2p($this->catBranch($product->cat_id)),
                    'product' => $productInfo
                ];
            }
        }

        return $output;
    }

    public function treeBranch($treeId = null)
    {
        if ($tree = \ss\models\Tree::find($treeId ?? $this->data('tree_id'))) {
            $branch = \ewma\Data\Tree::getBranch($tree);

            $output = [];

            foreach ($branch as $node) {
                $output[] = $node->name;
            }

            return $output;
        }
    }

    public function catBranch($catId = null)
    {
        if ($cat = \ss\models\Cat::find($catId ?? $this->data('cat_id'))) {
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
