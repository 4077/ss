<?php namespace ss\importLog\commander\panel\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

//    private $sPanel;

    private $s;

//    private $d;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

//            $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);

            $this->s = &$this->s('~|' . $this->_instance() . '/tree-' . $this->tree->id);
//            $this->d = &$this->d('~|tree-' . $this->tree->id);
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

    public function focus()
    {
        $id = $this->data('id');

        ap($this->s, 'focus', $id);
    }

    public function toggleChangeTypeFilter()
    {
        $type = $this->data('type');

        if (in($type, 'remote_cat_names_path, remote_name, price, units')) {
            $statusFilter = &ap($this->s, 'filters/changes_types/' . $type);

            invert($statusFilter);
        }

        $this->c('<:reload|');
    }

    public function open()
    {
        $id = $this->data('id');

        if ($product = \ss\models\Product::find($id)) {
            $commanderInstance = $this->panel->commander->instance;

            if (ss()->products->isEditable($product)) {
                $this->c('\ss\cats\cp dialogs:product|ss/commander', [
                    'product' => $product,
                    'ra'      => [
                        'callbacks' => [
                            'focus' => $this->_abs('\ss\commander\ui~app:disableKeyboard|' . $commanderInstance),
                            'close' => $this->_abs('\ss\commander\ui~app:enableKeyboard|' . $commanderInstance)
                        ]
                    ]
                ]);
            }
        }
    }

    public function diffDialog()
    {
        if ($change = \ss\models\ProductsChange::find($this->data('change_id'))) {
            $commanderInstance = $this->panel->commander->instance;

            $this->c('\std\ui\dialogs~:open:importDiffViewer, ss|ss/commander', [
                'path'          => '@diff:view|',
                'data'          => [
                    'change' => pack_model($change)
                ],
                'class'         => 'padding',
                'callbacks'     => [
                    'close' => $this->_abs('\ss\commander\ui~app:enableKeyboard|' . $commanderInstance)
                ],
                'pluginOptions' => [
                    'title' => $change->product->name
                ]
            ]);
        }
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
}
