<?php namespace ss\cp\trees\controllers\main\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function createFolders()
    {
        if ($node = $this->unxpackModel('node')) {
            $newNode = $node->nested()->create([
                                                   'mode' => 'folders'
                                               ]);

            $this->c('~:reload|');
        }
    }

    public function createPages()
    {
        if ($node = $this->unxpackModel('node')) {
            $newNode = $node->nested()->create([
                                                   'mode' => 'pages'
                                               ]);

            $this->c('~:reload|');
        }
    }

    public function duplicate()
    {
        if ($node = $this->unxpackModel('node')) {
            ss()->trees->duplicate($node);

            $this->c('~:reload|');
        }
    }

    public function rename()
    {
        if ($node = $this->unxpackModel('node')) {
            $txt = \std\ui\Txt::value($this);

            $node->name = $txt->value;
            $node->save();

            $txt->response();
        }
    }

    public function select()
    {
        if ($node = $this->unxpackModel('node')) {
            $this->s('~:selected_id|', $node->id, RR);

            $this->c('~:reload|');
        }
    }

    public function delete()
    {
        if ($this->dataHas('discarded')) {
            $this->c('\std\ui\dialogs~:close:tree_deleteConfirm|ss/trees');
        } else {
            if ($tree = $this->unxpackModel('node')) {
                $treesIds = \ewma\Data\Tree::getIds($tree);

                $cats = \ss\models\Cat::whereIn('tree_id', $treesIds)->get();
                $catsCount = count($cats);

                $products = \ss\models\Product::whereIn('tree_id', $treesIds)->get();
                $productsCount = count($products);

                $refsCount = 0;
                if ($productsIds = table_ids($products)) {
                    $refsInfo = ss()->products->getRefsInfo($productsIds);

                    $productsIdsWithRefs = $refsInfo->getAllProductsIds();

                    $refsCount = count($productsIdsWithRefs) - $productsCount;
                }

                if ($this->dataHas('confirmed')) {
                    // deleting

                    $this->c('\std\ui\dialogs~:close:tree_deleteConfirm|ss/trees');
                } else {
                    $this->c('\std\ui\dialogs~:open:tree_deleteConfirm, ss|ss/trees', [
                        'path'            => '@delete:view',
                        'data'            => [
                            'confirm_call'   => $this->_abs(':delete|', ['tree' => xpack_model($tree)]),
                            'discard_call'   => $this->_abs(':delete|', ['tree' => xpack_model($tree)]),
                            'cats_count'     => $catsCount,
                            'products_count' => $productsCount,
                            'refs_count'     => $refsCount
                        ],
                        'forgot_on_leave' => false,
                        'forgot_on_close' => false,
                        'pluginOptions'   => [
                            'resizable' => false
                        ]
                    ]);
                }

                return 0;
            }

            if ($cat = \ss\models\Cat::find($this->data['cat_id'])) {
                $catsIds = \ewma\Data\Tree::getIds($cat);

                $nestedCatsCount = count($catsIds) - 1;

                $products = \ss\models\Product::whereIn('cat_id', $catsIds)->get();
                $productsCount = count($products);

                if ($this->dataHas('confirmed') || (!$nestedCatsCount && !$productsCount)) {
                    \ss\models\Product::whereIn('cat_id', $catsIds)->update([
                                                                                'cat_id' => ss()->cats->getRootCat()->id
                                                                            ]);

                    \ss\models\Cat::whereIn('id', $catsIds)->delete();

                    $this->c('~|')->performCallback('delete', [
                        'deleted_cats_ids' => $catsIds
                    ]);

                    $this->c('\std\ui\dialogs~:close:tree_deleteConfirm|ss/trees');
                } else {
                    $this->c('\std\ui\dialogs~:open:tree_deleteConfirm, ss|ss/trees', [
                        'path'            => 'deleteConfirm~:view',
                        'data'            => [
                            'confirm_call'      => $this->_abs(':delete|', ['cat_id' => $cat->id]),
                            'discard_call'      => $this->_abs(':delete|', ['cat_id' => $cat->id]),
                            'cat_name'          => $cat->name,
                            'products_count'    => $productsCount,
                            'nested_cats_count' => $nestedCatsCount
                        ],
                        'forgot_on_leave' => true,
                        'forgot_on_close' => true,
                        'pluginOptions'   => [
                            'resizable' => false
                        ]
                    ]);
                }
            }
        }
    }

//    public function catDialog()
//    {
//        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
//            $this->c('^cp/cats app:catDialog', [
//                'cat'                        => $cat,
//                'dialogs_container_instance' => 'ss/cp/products'
//            ]);
//        }
//    }
}
