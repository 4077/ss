<?php namespace ss\products\cp\props\controllers;

class Main extends \Controller
{
    private $product;

    public function __create()
    {
        $this->product = $this->unpackModel('product');

        $this->instance_($this->product->id);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $product = $this->product;

        $productXPack = xpack_model($product);

        $props = _j($product->props);

        foreach ((array)$props as $n => $prop) {
            $v->assign('prop', [
                'NUMBER'        => $n,
                'LABEL_TXT'     => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:labelUpdate',
                    'data'              => [
                        'product' => $productXPack,
                        'number'  => $n
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.label',
                    'content'           => $prop['label']
                ]),
                'VALUE_TXT'     => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:valueUpdate',
                    'data'              => [
                        'product' => $productXPack,
                        'number'  => $n
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.value',
                    'content'           => $prop['value']
                ]),
                'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:delete',
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
                           'path'    => '>xhr:add',
                           'data'    => [
                               'product' => $productXPack
                           ],
                           'class'   => 'add_button green',
                           'content' => 'Добавить'
                       ])
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:ss/cp/products/props');

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('. .props'),
            'items_id_attr'  => 'prop_number',
            'path'           => '>xhr:reorder',
            'data'           => [
                'product' => $productXPack
            ],
            'plugin_options' => [
                'distance' => 15
            ]
        ]);

        $this->css(':\css\std~');

        $this->e('ss/products/props/update')->rebind(':reload');

        return $v;
    }
}
