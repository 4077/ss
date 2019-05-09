<?php namespace ss\controllers\main;

class Eraser extends \Controller
{
    // products

    public function deleteTreeProducts()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            $builder = $tree->products();

            $count = $builder->count();

            $builder->delete();

            return 'delete products in tree ' . $tree->id . ', deleted count: ' . $count;
        }
    }

    public function deleteCatProducts()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $builder = $cat->products();

            $count = $builder->count();

            $builder->delete();

            return 'delete products in cat ' . $cat->id . ', deleted count: ' . $count;
        }
    }

    public function deleteCatProductsRecursive()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $cat->tree_id))->getIds($cat);

            $builder = \ss\models\Product::whereIn('cat_id', $catsIds);

            $count = $builder->count();

            $builder->delete();

            return 'recursive delete products in cat ' . $cat->id . ', deleted count: ' . $count;
        }
    }

    // stock reset

    public function resetTreeProductsStock()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            $builder = $tree->products();

            $count = $builder->count();

            $this->resetStock($builder->get());

            return 'set stock 0 for tree ' . $tree->id . ' products, updated count: ' . $count;
        }
    }

    public function resetCatProductsStock()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $builder = $cat->products();

            $count = $builder->count();

            $this->resetStock($builder->get());

            return 'set stock 0 for cat ' . $cat->id . ' products, updated count: ' . $count;
        }
    }

    public function resetCatProductsStockRecursive()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $cat->tree_id))->getIds($cat);

            $builder = \ss\models\Product::whereIn('cat_id', $catsIds);

            $count = $builder->count();

            $this->resetStock($builder->get());

            return 'recursive set stock 0 for cat ' . $cat->id . ' products, deleted count: ' . $count;
        }
    }

    private function resetStock($products)
    {
        $count = count($products);
        $n = 0;

        foreach ($products as $product) {
            $n++;

            $msc = _j($product->multisource_cache);

            foreach ($msc as $divisionId => $divisionData) {
                $warehouses = $divisionData['warehouses'] ?? [];

                foreach ($warehouses as $warehouseId => $warehouseData) {
                    ss()->products->updateMultisourceData($product, [
                        'warehouse_id' => $warehouseId,
                        'stock'        => 0,
                        'price'        => 0
                    ]);
                }

                $this->log($n . '/' . $count . ' set stock 0 for product ' . $product->id);
            }
        }
    }

    // clear multisource data

    public function clearProductMultisourceData()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            $this->clearMultisourceData([$product]);
        }
    }

    public function clearTreeProductMultisourceData()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            $builder = $tree->products();

            $count = $builder->count();

            $this->clearMultisourceData($builder->get());

            return 'clear multisource data for tree ' . $tree->id . ' products, updated count: ' . $count;
        }
    }

    public function clearCatProductMultisourceData()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $builder = $cat->products();

            $count = $builder->count();

            $this->clearMultisourceData($builder->get());

            return 'clear multisource data for cat ' . $cat->id . ' products, updated count: ' . $count;
        }
    }

    public function clearCatProductMultisourceDataRecursive()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $cat->tree_id))->getIds($cat);

            $builder = \ss\models\Product::whereIn('cat_id', $catsIds);

            $count = $builder->count();

            $this->clearMultisourceData($builder->get());

            return 'recursive clear multisource data for cat ' . $cat->id . ' products, updated count: ' . $count;
        }
    }

    private function clearMultisourceData($products)
    {
        $count = count($products);
        $n = 0;

        foreach ($products as $product) {
            $n++;

            ss()->products->clearMultisourceData($product);

            $this->log($n . '/' . $count . ' clear multisource data for product ' . $product->id);
        }
    }
}
