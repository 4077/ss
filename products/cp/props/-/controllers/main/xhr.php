<?php namespace ss\products\cp\props\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private function updateSource($product)
    {
        if ($source = $product->source) {
            $source->props = $product->props;
            $source->save();
        }
    }

    public function add()
    {
        if ($product = $this->unxpackModel('product')) {
            $props = _j($product->props);

            $props[] = [
                'label' => '',
                'value' => ''
            ];

            $product->props = j_($props);
            $product->save();

            $this->updateSource($product);

            $this->e('ss/products/props/update')->trigger(['product' => $product]);
//            $this->c('^cp/products app:triggerProductUpdate', ['product' => $product]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/cp/products/props');
        } else {
            if ($product = $this->unxpackModel('product')) {
                $props = _j($product->props);

                $propNumber = $this->data['number'];

                if ($this->data('confirmed')) {
                    unset($props[$propNumber]);
                    array_values($props);

                    $product->props = j_($props);
                    $product->save();

//                    $this->c('^cp/products app:updateProductSearchCache', [
//                        'product' => $product
//                    ]);

                    $this->updateSource($product);

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/cp/products/props');

                    $this->e('ss/products/props/update')->trigger(['product' => $product]);
//                    $this->c('^cp/products app:triggerProductUpdate', ['product' => $product]);
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|ss/cp/products/props', [
                        'path' => '\std dialogs/confirm~:view',
                        'data' => [
                            'confirm_call' => $this->_abs(':delete', [
                                'number'  => $propNumber,
                                'product' => $this->data['product']
                            ]),
                            'discard_call' => $this->_abs(':delete', [
                                'number'  => $propNumber,
                                'product' => $this->data['product']
                            ]),
                            'message'      => 'Удалить <b>' . $props[$propNumber]['label'] . '</b>?'
                        ]
                    ]);
                }
            }
        }
    }

    public function labelUpdate()
    {
        if ($product = $this->unxpackModel('product')) {
            $props = _j($product->props);

            if (isset($props[$this->data('number')])) {
                $txt = \std\ui\Txt::value($this);

                $props[$this->data('number')]['label'] = $txt->value;

                $product->props = j_($props);
                $product->save();

                $txt->response();

                $this->updateSource($product);

                $this->e('ss/products/props/update')->trigger(['product' => $product]);
//                $this->c('^cp/products app:triggerProductUpdate', ['product' => $product]);
            }
        }
    }

    public function valueUpdate()
    {
        if ($product = $this->unxpackModel('product')) {
            $props = _j($product->props);

            if (isset($props[$this->data('number')])) {
                $txt = \std\ui\Txt::value($this);

                $props[$this->data('number')]['value'] = $txt->value;

                $product->props = j_($props);
                $product->save();

                $txt->response();

//                $this->c('^cp/products app:updateProductSearchCache', [
//                    'product' => $product
//                ]);

                $this->updateSource($product);

                $this->e('ss/products/props/update')->trigger(['product' => $product]);
//                $this->c('^cp/products app:triggerProductUpdate', ['product' => $product]);
            }
        }
    }

    public function reorder()
    {
        $product = $this->unxpackModel('product');

        if ($product && $this->dataHas('sequence')) {
            $props = _j($product->props);

            $propsNew = [];
            foreach ($this->data['sequence'] as $number) {
                $propsNew[] = $props[$number];
            }

            $product->props = j_($propsNew);
            $product->save();

            $this->updateSource($product);

            $this->e('ss/products/props/update')->trigger(['product' => $product]);
//            $this->c('^cp/products app:triggerProductUpdate', ['product' => $product]);

//            $this->c('^cp/products app:updateProductSearchCache', [
//                'product' => $product
//            ]);
        }
    }
}
