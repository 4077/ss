<?php namespace ss\cats\cp\product\props\controllers;

class Main extends \Controller
{
    private $product;

    private $viewInstance;

    public function __create()
    {
        $this->product = $this->unpackModel('product');

        $this->viewInstance = $this->product->id;
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $product = $this->product;
        $productXPack = xpack_model($product);

        $props = _j($product->props);

        foreach ((array)$props as $n => $prop) {
            $v->assign('prop', [
                'NUMBER'        => $n,
                'LABEL_TXT'     => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateLabel|',
                    'data'              => [
                        'product' => $productXPack,
                        'number'  => $n
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.label',
                    'content'           => $prop['label']
                ]),
                'VALUE_TXT'     => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateValue|',
                    'data'              => [
                        'product' => $productXPack,
                        'number'  => $n
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.value',
                    'content'           => $prop['value']
                ]),
                'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:delete|',
                    'data'    => [
                        'product' => $productXPack,
                        'number'  => $n
                    ],
                    'class'   => 'delete_button',
                    'title'   => 'Удалить',
                    'content' => '<div class="icon"></div>'
                ])
            ]);
        }

        $v->assign([
                       'ADD_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:add|',
                           'data'    => [
                               'product' => $productXPack
                           ],
                           'class'   => 'add_button green',
                           'content' => 'Добавить'
                       ])
                   ]);

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('|' . $this->viewInstance) . ' .props',
            'items_id_attr'  => 'prop_number',
            'path'           => '>xhr:arrange',
            'data'           => [
                'product' => $productXPack
            ],
            'plugin_options' => [
                'distance' => 15,
                'axis'     => 'y'
            ]
        ]);

        $this->css(':\css\std~');

        $this->widget(':|', [
//            '.e' => [
////                'ss/product/' . $product->id . '/update_props' => 'mr.reload' //
//            ],
'.r' => [
    'reload' => $this->_abs('>xhr:reload|', [
        'product' => $productXPack
    ])
]
        ]);

        return $v;
    }
}
