<?php namespace ss\products\cp\controls\props\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function openDialog()
    {
        if ($product = $this->unxpackModel('product')) {
            $this->c('\std\ui\dialogs~:open:props|' . $this->data('dialogs_container_instance'), [
                'path'          => '\ss\products\cp\props~:view',
                'data'          => [
                    'product' => pack_model($product)
                ],
                'pluginOptions' => [
                    'title' => 'Характеристики товара ' . ($product->name ? $product->name : '...')
                ],
                'default'       => [
                    'pluginOptions' =>
                        [
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
