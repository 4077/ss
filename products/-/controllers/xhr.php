<?php namespace ss\products\controllers;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function openDialog()
    {
        if ($product = $this->unxpackModel('product')) {
            $this->c('\std\ui\dialogs~:open:product, ss|ss/trees', [
                'path' => '#ss/ui/products:form',
                'data' => [
                    'product' => pack_model($product)
                ],

            ]);
        }
    }

    public function delete()
    {
        if ($product = $this->unxpackModel('product')) {

        }
    }
}
