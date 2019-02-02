<?php namespace ss\cp\orders\controllers;

class Main extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $v->assign([
                       'NAV'    => $this->c('>nav:view'),
                       'ORDERS' => $this->c('>orders:view')
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:ss/cp/orders');

        $this->css();

        return $v;
    }
}
