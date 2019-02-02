<?php namespace ss\cats\cp\product\props\controllers\main;

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

    private function update($product, $props)
    {
        $updatedProducts = ss()->products->update($product, [
            'props' => j_($props)
        ]);

        foreach ($updatedProducts as $updatedProduct) {
            pusher()->trigger('ss/product/update', [
                'id'    => $updatedProduct->id,
                'props' => true
            ]);
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

            $this->update($product, $props);

            $this->c('<:reload|', [
                'product' => $product
            ]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|');
        } else {
            if ($product = $this->unxpackModel('product')) {
                $props = _j($product->props);

                $propNumber = $this->data['number'];

                if ($this->data('confirmed')) {
                    unset($props[$propNumber]);
                    array_values($props);

                    $this->update($product, $props);

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|');

                    $this->c('<:reload|', [
                        'product' => $product
                    ]);
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|', [
                        'path' => '\std dialogs/confirm~:view',
                        'data' => [
                            'confirm_call' => $this->_abs(':delete|', [
                                'number'  => $propNumber,
                                'product' => $this->data['product']
                            ]),
                            'discard_call' => $this->_abs(':delete|', [
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

    public function updateLabel()
    {
        if ($product = $this->unxpackModel('product')) {
            $props = _j($product->props);

            if (isset($props[$this->data('number')])) {
                $txt = \std\ui\Txt::value($this);

                $props[$this->data('number')]['label'] = $txt->value;

                $this->update($product, $props);

                $txt->response();
            }
        }
    }

    public function updateValue()
    {
        if ($product = $this->unxpackModel('product')) {
            $props = _j($product->props);

            if (isset($props[$this->data('number')])) {
                $txt = \std\ui\Txt::value($this);

                $props[$this->data('number')]['value'] = $txt->value;

                $this->update($product, $props);

                $txt->response();
            }
        }
    }

    public function arrange()
    {
        $product = $this->unxpackModel('product');

        if ($product && $this->dataHas('sequence')) {
            $props = _j($product->props);

            $propsNew = [];
            foreach ($this->data['sequence'] as $number) {
                $propsNew[] = $props[$number];
            }

            $this->update($product, $propsNew);

            $this->c('<:reload|', [
                'product' => $product
            ]);
        }
    }
}
