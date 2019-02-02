<?php namespace ss\cats\controllers;

class Main extends \Controller
{
    public function get()
    {
        $builder = \ss\models\Cat::query();

        if ($id = $this->data('id')) {
            $builder = $builder->where('id', $id);
        }

        if ($treeId = $this->data('tree_id')) {
            $builder = $builder->where('tree_id', $treeId);
        }

        if ($parentId = $this->data('parent_id')) {
            $builder = $builder->where('parent_id', $parentId);
        }

        if ($articul = $this->data('articul')) {
            $builder = $builder->where('articul', $articul);
        }

        if ($this->dataHas('route')) {
            $route = $this->data('route');

            if (!$route && $rootIdForEmptyRoute = $this->data('root_id_for_empty_route')) {
                $builder = $builder->where('id', $rootIdForEmptyRoute);
            } else {
                $builder = $builder->where('route_cache', trim_slashes($route));
            }
        }

        $cat = $builder->first();

        return $cat;
    }

    public function getPageCatByProduct()
    {
        if ($product = $this->data('product') and $product instanceof \ss\models\Product) {
            if ($container = $product->cat) {
                if ($page = $container->parent) {
                    return $page;
                }
            }
        }
    }

    public function getCatByProduct()
    {
        if ($product = $this->data('product') and $product instanceof \ss\models\Product) {
            if ($cat = $product->cat) {
                return $cat;
            }
        }
    }

    public function getPageCatByProductId()
    {
        $productId = $this->data('product_id');

        if ($product = \ss\models\Product::find($productId)) {
            if ($container = $product->cat) {
                if ($page = $container->parent) {
                    return $page;
                }
            }
        }
    }

    public function getProduct()
    {
        $builder = \ss\models\Product::query();

        if ($treeId = $this->data('tree_id')) {
            $builder = $builder->where('tree_id', $treeId);
        }

        if ($articul = $this->data('articul')) {
            $builder = $builder->where('articul', $articul);
        }

        if ($id = $this->data('id')) {
            $builder = $builder->where('id', $id);
        }

        $product = $builder->first();

        return $product;
    }
}
