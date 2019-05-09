<?php namespace ss\flow\ui\channel\collationInfo\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateScroll()
    {
        $this->s('~:scrolls/' . $this->data('type') . '|', $this->data('value'), RR);
    }

    public function updateScrolls()
    {
        $s = &$this->s('~:scrolls|');

        remap($s, $this->data, 'connections, sources, targets');
    }

    public function openProduct()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            $this->c('\std\ui\dialogs~:open:product, ss|ss/flow/channels', [
                'path'          => '\ss\cats\cp\product~:view|',
                'data'          => [
                    'product' => pack_model($product)
                ],
                //            'title'   => $this->_abs('\ss\cats\cp\product~dialogTitle:view', [
                //                'cat' => pack_model($product)
                //            ]),
                'class'         => 'padding',
                'pluginOptions' => [
                    'title' => $product->name
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 800,
                        'height' => 600
                    ]
                ]
            ]);
        }
    }
}
