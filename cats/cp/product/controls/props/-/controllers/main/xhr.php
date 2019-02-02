<?php namespace ss\cats\cp\product\controls\props\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($product = $this->unxpackModel('product')) {
            $this->c('<:reload|', [
                'product' => $product
            ]);
        }
    }

    public function openDialog()
    {
        if ($product = $this->unxpackModel('product')) {
            $this->c('\std\ui\dialogs~:open:props, ss|', [
                'path'          => '<<props~:view|',
                'data'          => [
                    'product' => pack_model($product)
                ],
                'class'         => 'padding',
                'pluginOptions' => [
                    'title' => 'Характеристики товара ' . ($product->name ? $product->name : '...')
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 500,
                        'height' => 400,
                    ]
                ]
            ]);

            $this->e('ss/products/delete', ['product_id' => $product->id])->rebind('\std\ui\dialogs~:close:props' . $this->data('dialogs_container_instance'));
            $this->e('ss/products/delete_all')->rebind('\std\ui\dialogs~:close:props' . $this->data('dialogs_container_instance'));
        }
    }
}
