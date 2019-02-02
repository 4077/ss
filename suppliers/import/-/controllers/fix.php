<?php namespace ss\suppliers\import\controllers;

class Fix extends \Controller
{
    public function alfaCatsTrimSpace()
    {
        $cats = \ss\models\Cat::where('tree_id', 31)->get();

        $tree = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', 31));

        $count = count($cats);
        $n = 0;

        foreach ($cats as $cat) {
            $branch = $tree->getBranch($cat->id);

            $nameBranch = array_slice(table_column($branch, 'name'), 1);

            $newNameBranch = array_map('trim', $nameBranch);
            $newArticul = 'ALFAKERAMIKA' . jmd5($newNameBranch);

            $updateData = [
                'articul' => $newArticul,
                'name'    => trim($cat->name)
            ];

            $cat->update($updateData);

            $this->log(++$n . '/' . $count . ' ' . $cat->name);
        }
    }

    public function alfaProductsChangeFields()
    {
        $products = \ss\models\Product::where('tree_id', 31)->get();

        $count = count($products);
        $n = 0;

        foreach ($products as $product) {
            $updateData = [
                'articul'        => 'ALFAKERAMIKA' . $product->name,
                'remote_articul' => $product->name
            ];

            $product->update($updateData);

            $this->log(++$n . '/' . $count . ' ' . $product->name);
        }
    }
}
