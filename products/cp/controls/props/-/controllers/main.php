<?php namespace ss\products\cp\controls\props\controllers;

class Main extends \Controller
{
    private $product;

    public function __create()
    {
        $this->product = $this->data['product'];

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

        $v->assign('CONTENT', $this->getPropsString($product));

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|'),
            'path'     => '>xhr:openDialog',
            'data'     => [
                'product'                    => xpack_model($product),
                'dialogs_container_instance' => $this->data('dialogs_container_instance')
            ]
        ]);

        $this->css();

        $this->e('ss/products/props/update')->rebind(':reload', [
            'dialogs_container_instance' => $this->data('dialogs_container_instance')
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

        return $list ? implode('; ', $list) : '...';
    }
}
