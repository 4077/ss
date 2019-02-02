<?php namespace ss\cp\orders\controllers\main\orders;

use ss\models\Order as OrderModel;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private $order;

    private function getOrder()
    {
        if (null === $this->order) {
            $this->order = OrderModel::find($this->data('order_id'));
        }

        return $this->order;
    }

    private function ordersViewReload()
    {
        $this->c('~orders:reload');
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/cp/orders');
        } else {
            if ($order = $this->getOrder()) {
                if ($this->data('confirmed')) {
                    $order->delete();

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/cp/orders');
                    $this->ordersViewReload();
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm, ss|ss/cp/orders', [
                        'path' => '\std dialogs/confirm~:view',
                        'data' => [
                            'confirm_call' => $this->_abs(':delete', ['order_id' => $order->id]),
                            'discard_call' => $this->_abs(':delete', ['order_id' => $order->id]),
                            'message'      => 'Удалить заказ <b>#' . $order->id . '</b>?'
                        ]
                    ]);
                }
            }
        }
    }
}
