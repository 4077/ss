<?php namespace ss\cats\controllers;

class Fix extends \Controller
{
    public function resetCatsLess()
    {
        \ss\models\Cat::query()->update(['less' => '']);
    }

    public function deleteProductsInTree()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $ids = \ewma\Data\Tree::getIds($cat);

            return \ss\models\Product::whereIn('cat_id', $ids)->delete();
        }
    }

    public function deleteOrphanedImportChanges()
    {
        return \ss\models\ProductsChange::doesntHave('product')->delete();
    }

    public function deleteNestedCatsAndProducts()
    {
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
        $cats = \ss\models\Cat::all();

        $output = [];

        foreach ($cats as $cat) {
            if ($cat->id == $cat->parent_id) {
                $output[] = $cat->id;
            }
        }

        return $output;
    }

    //
    // переносчик товаров из старой таблицы тд в таблицу сс
    //

    public function td2ss()
    {
        $type = $this->data('type');
        $targetCatId = $this->data('cat_id');

        $tdProducts = \td\products\models\Product::where('type', $type)->orderBy('position')->get();

        foreach ($tdProducts as $tdProduct) {
            $tdData = $tdProduct->toArray();
            $ssData = [];

            remap($ssData, $tdData, '
                name,
                units,
                price       unit_price,
                alt_units   sell_units,
                alt_price   sell_unit_price,
                props            
            ');

            $ssData['cat_id'] = $targetCatId;

            $ssProduct = \ss\models\Product::create($ssData);

            $this->c('\std\images~:copy', [
                'source' => $tdProduct,
                'target' => $ssProduct
            ]);
        }
    }
}
