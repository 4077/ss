<?php namespace ss\stockPhotoRequest\commander\panel\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sPanel;

    private $s;

    private $d;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

            $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);

            $this->s = &$this->s('~|' . $this->_instance() . '/tree-' . $this->tree->id);
            $this->d = &$this->d('~|tree-' . $this->tree->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->c('<:reload|');
    }

    public function setPage($value)
    {
        $this->s['page'] = $value;

        $this->c('<:reload|');
    }

    public function toggleStatusFilter()
    {
        $status = $this->data('status');

        $pending = &ap($this->s, 'filters/status/pending');
        $done = &ap($this->s, 'filters/status/done');

        if ($status == 'pending') {
            invert($pending);

            if (!$pending && !$done) {
                $done = true;
            }
        }

        if ($status == 'done') {
            invert($done);

            if (!$pending && !$done) {
                $pending = true;
            }
        }

        $this->c('<:reload|');
    }

    public function setFilterUser()
    {
        $id = $this->data('value');

        ap($this->s, 'filters/user_id', $id);

        $this->c('<:reload|');
    }

    public function focus()
    {
        $id = $this->data('id');

        $tree = $this->tree;
        $panel = $this->panel;

        $s = &$this->s('~|' . $panel->instance . '/tree-' . $tree->id);

        ra($s, [
            'focus' => $id
        ]);
    }

    public function select()
    {

    }

//    public function select()
//    {
//        $id = $this->data('id');
//
//        if ($localProduct = \ss\models\Product::find($id)) {
//            $importProduct = \ss\models\Product::where('id', $localProduct->source_id)->first();
//
//            if ($importProduct) {
//                $this->s('~import:cat_id|', $importProduct->cat_id, RR);
//
//                $this->s('~import:selection|', [
//                    'type' => 'product',
//                    'id'   => $importProduct->id
//                ], RR);
//
//                $this->s('~import:focus|', [
//                    'type' => 'product',
//                    'id'   => $importProduct->id
//                ], RR);
//            } else {
//                $this->s('~import:selection|', [
//                    'type' => null,
//                    'id'   => null
//                ], RR);
//            }
//
//            $this->s('~local:cat_id|', $localProduct->cat->parent_id, RR);
//
//            $this->s('~local:selection|', [
//                'type' => 'product',
//                'id'   => $localProduct->id
//            ], RR);
//
//            $this->s('~local:focus|', [
//                'type' => 'product',
//                'id'   => $localProduct->id
//            ], RR);
//
//            $this->c('~local:reload|');
//            $this->c('~import:reload|');
//        }
//    }

    public function selectUser()
    {
        ap($this->s, 'target_user_id', $this->data('value'));
    }

    public function add()
    {
        $items = $this->data('items');

        $productsIds = [];

        foreach ($items as $item) {
            if ($item['type'] == 'product') {
                $productsIds[] = $item['id'];
            }
        }

        $sourceUserId = $this->_user('id');
        $targetUserId = ap($this->s, 'target_user_id');

        $addedProducts = \ss\stockPhotoRequest\models\Request::where('tree_id', $this->tree->id)
            ->whereIn('product_id', $productsIds)
            ->get();

        $addedProductsIds = table_cells($addedProducts, 'product_id');

        \ss\stockPhotoRequest\models\Request::where('tree_id', $this->tree->id)
            ->whereIn('product_id', $addedProductsIds)
            ->where('to_user_id', '!=', $targetUserId)
            ->update([
                         'to_user_id' => $targetUserId
                     ]);

        $addProductsIds = diff($productsIds, $addedProductsIds, true);

        foreach ($addProductsIds as $addProductId) {
            \ss\stockPhotoRequest\models\Request::create([
                                                             'tree_id'          => $this->tree->id,
                                                             'product_id'       => $addProductId,
                                                             'from_user_id'     => $sourceUserId,
                                                             'to_user_id'       => $targetUserId,
                                                             'request_datetime' => \Carbon\Carbon::now()->toDateTimeString()
                                                         ]);
        }

        $this->c('<:reload|');
    }

    public function notifyDialog()
    {
        if ($tree = $this->unxpackModel('tree')) {
            $commanderInstance = $this->panel->commander->instance;

            $this->c('\std\ui\dialogs~:open:SPRnotifyDialog, ss|ss/commander', [
                'path'          => '\ss\stockPhotoRequest\ui notify:view',
                'data'          => [
                    'tree' => pack_model($tree)
                ],
                'class'         => 'padding',
                'callbacks'     => [
                    'close' => $this->_abs('\ss\commander\ui~app:enableKeyboard|' . $commanderInstance)
                ],
                'pluginOptions' => [
                    'title' => ''
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 400,
                        'height' => 300
                    ]
                ]
            ]);

            $this->c('\ss\commander\ui~app:disableKeyboard|' . $commanderInstance);
        }
    }

//    public function open()
//    {
//        $id = $this->data('id');
//
//        if ($product = \ss\models\Product::find($id)) {
//            if (ss()->products->isEditable($product)) {
//                $commanderInstance = $this->panel->commander->instance;
//
//                $this->c('\std\ui\dialogs~:open:product, ss|ss/commander', [
//                    'path'          => 'dialogs/wrapper:view|',
//                    'data'          => [
//                        'product' => pack_model($product),
//                        'type'    => 'product'
//                    ],
//                    'class'         => 'padding',
//                    'callbacks'     => [
//                        'close' => $this->_abs('\ss\commander\ui~app:enableKeyboard|' . $commanderInstance)
//                    ],
//                    'pluginOptions' => [
//                        'title' => $product->name
//                    ],
//                    'default'       => [
//                        'pluginOptions' => [
//                            'width'  => 800,
//                            'height' => 600
//                        ]
//                    ]
//                ]);
//            }
//        }
//    }
}
