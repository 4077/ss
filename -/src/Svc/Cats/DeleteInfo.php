<?php namespace ss\Svc\Cats;

class DeleteInfo
{
    private $cat;

    public function __construct(\ss\models\Cat $cat)
    {
        $this->cat = $cat;
    }

    public function render()
    {
        $cat = $this->cat;

        $tree = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $cat->tree_id));

        $output = [
            'tree'    => [],
            'summary' => [
                'nested_cats' => [
                    'total'     => 0,
                    'deletable' => 0
                ],
                'products'    => [
                    'total'     => 0,
                    'deletable' => 0
                ],
                'refs'        => [
                    'total'     => 0,
                    'deletable' => 0 // когда будет проверка прав на удаление в связанных ветках
                ]
            ]
        ];

        $level = 0;

        $treeRecursion = function ($cat) use (&$treeRecursion, $tree, &$level, &$output) {
            $subcats = $tree->getSubnodes($cat->id);

            $level++;

            $hasDeniedSubcats = false;

            foreach (array_reverse($subcats) as $subcat) {
                $subcatDenied = $treeRecursion($subcat);

                $hasDeniedSubcats |= $subcatDenied;
            }

            $level--;

            $deniedByAccess = !ss()->cats->isCDable($cat);
            $deniedByProducts = false;

            $products = $cat->products()->get();
            $productsCount = count($products);

            $refsInfo = false;

            if ($productsCount) {
                $productsCDable = ss()->cats->isProductsCDable($cat);

                $deniedByProducts = !$productsCDable;

                $output['summary']['products']['total'] += $productsCount;

                if ($productsCDable) {
                    $output['summary']['products']['deletable'] += $productsCount;
                }

                $refsInfo = ss()->products->getRefsInfo(table_ids($products));
            }

            $denied = $deniedByAccess || $hasDeniedSubcats || $deniedByProducts;

            $output['tree'][] = [
                'level'              => $level,
                'cat'                => $cat,
                'denied'             => $denied,
                'denied_by_access'   => $deniedByAccess,
                'denied_by_nested'   => $hasDeniedSubcats,
                'denied_by_products' => $deniedByProducts,
                'products'           => $productsCount ? table_rows_by_id($products) : [],
                'refs_info'          => $refsInfo
            ];

            $output['summary']['nested_cats']['total']++;

            if (!$denied) {
                $output['summary']['nested_cats']['deletable']++;
            }

            if ($refsInfo) {
                $refsIds = $refsInfo->getRefsIds();

                $output['summary']['refs']['total'] += count($refsIds);
            }

            return $denied || $hasDeniedSubcats || $deniedByProducts;
        };

        $treeRecursion($cat);

        $output['tree'] = array_reverse($output['tree']);

        return $output;
    }
}
