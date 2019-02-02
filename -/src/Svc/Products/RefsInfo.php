<?php namespace ss\Svc\Products;

class RefsInfo
{
    private $inputProductsIds = [];

    /**
     * RefsInfo constructor.
     *
     * @param $productsIds array - products ids of one(!) tree
     */
    public function __construct($productsIds)
    {
        $this->inputProductsIds = $productsIds;
    }

    private $treesById = [];

    private $productIdsByTreeId = [];

    private $catsIdsByTreeId = [];

    private function addProducts(\ss\models\Tree $tree, $productsIds)
    {
        $this->treesById[$tree->id] = $tree;
        $this->productIdsByTreeId[$tree->id] = $productsIds;

        $products = \ss\models\Product::whereIn('id', $productsIds)->groupBy('cat_id')->get();

        $this->catsIdsByTreeId[$tree->id] = table_cells($products, 'cat_id');
    }

    public function render()
    {
        if (isset($this->inputProductsIds[0])) {
            if ($product0 = \ss\models\Product::find($this->inputProductsIds[0])) {
                $collectDescendants = function ($tree, $ids) use (&$collectDescendants) {
                    $this->addProducts($tree, $ids);

                    $descendants = ss()->trees->connections->getDescendants($tree);

                    foreach ($descendants as $descendant) {
                        $targetTree = $descendant->target;

                        $targetProducts = $targetTree->products()->whereIn('source_id', $ids)->get();

                        if (count($targetProducts)) {
                            $collectDescendants($targetTree, table_ids($targetProducts));
                        }
                    }
                };

                $collectDescendants($product0->tree, $this->inputProductsIds);
            }
        }

        return $this;
    }

    public function getAllProductsIds()
    {
        $output = [];

        foreach ($this->productIdsByTreeId as $treeId => $productsIds) {
            merge($output, $productsIds);
        }

        return $output;
    }

    public function getRefsIds()
    {
        $ids = $this->getAllProductsIds();

        diff($ids, $this->inputProductsIds);

        return $ids;
    }

    public function getAllCatsIds()
    {
        $output = [];

        foreach ($this->catsIdsByTreeId as $treeId => $catsIds) {
            merge($output, $catsIds);
        }

        return $output;
    }
}