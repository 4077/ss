<?php namespace ss\cats\cp\product\controllers\divisionsData;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function historyDialog()
    {
        if ($product = $this->unxpackModel('product')) {
            $this->c('\std\ui\dialogs~:open:divisionsDataHistory, ss|', [
                'path' => '@history:view|',
                'data' => [
                    'product' => pack_model($product),
                    'type'    => $this->data('type'),
                    'id'      => $this->data('id')
                ]
            ]);
        }
    }
}
