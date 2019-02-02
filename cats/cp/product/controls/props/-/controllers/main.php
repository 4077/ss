<?php namespace ss\cats\cp\product\controls\props\controllers;

class Main extends \Controller
{
    private $product;

    private $viewInstance;

    public function __create()
    {
        $this->product = $this->data['product'];

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

        $v->assign('CONTENT', $this->getPropsString($product));

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $this->viewInstance),
            'path'     => '>xhr:openDialog|',
            'data'     => [
                'product' => xpack_model($product)
            ]
        ]);

        $this->css();

        $this->widget(':|' . $this->viewInstance, [
            '.e'        => [
                'ss/product/update' => 'onPropsUpdate'
            ],
            '.r'        => [
                'reload' => $this->_abs('>xhr:reload|', [
                    'product' => xpack_model($product)
                ])
            ],
            'productId' => $product->id
        ]);

        return $v;
    }

    private function getPropsString($product)
    {
        $props = _j($product->props);

        $list = [];

        foreach ((array)$props as $prop) {
            if ($prop['label']) {
                $list[] = $prop['label'] . ': ' . $prop['value'];
            } else {
                $list[] = $prop['value'];
            }
        }

        return implode('; ', $list) ?: '...';
    }
}
