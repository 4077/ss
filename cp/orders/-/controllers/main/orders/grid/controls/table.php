<?php namespace ss\cp\orders\controllers\main\orders\grid\controls;

class Table extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $order = $this->data['order'];

        $items = _j($order->items);
        $deliveryData = _j($order->delivery_data);

        $deliveryCost = isset($deliveryData['cost']) ? $deliveryData['cost'] : null;
        $deliveryOptions = isset($deliveryData['options']) ? $deliveryData['options'] : [];

        $totalCost = 0;

        foreach ($items as $item) {
            $cost = $item['price'] * $item['quantity'];

            $totalCost += $cost;

            $item = $this->fixItemData($item);

            $v->assign('item', [
                'NAME'     => $item['name'] ? $item['name'] : '...',
                'PROPS'    => $this->getPropsString($item),
                'PRICE'    => number_format__($item['price']),
                'QUANTITY' => trim_zeros($item['quantity']),
                'COST'     => number_format__($cost)
            ]);
        }

        if (null !== $deliveryCost) {
            if ($deliveryCost < 0) { // по идее не нужно так как при сохраненнии ограничивается нулем
                $deliveryCost = 0;
            }

            $v->assign('delivery', [
                'ORDER_TOTAL_COST' => number_format__($totalCost),
                'COST'             => number_format__($deliveryCost)
            ]);

            foreach ($deliveryOptions as $deliveryOption) {
                $v->assign('delivery/option', [
                    'NAME'        => $deliveryOption['name'],
                    'DESCRIPTION' => $deliveryOption['description'],
                    'OPERATOR'    => $this->getDeliveryOptionOperator($deliveryOption['operator']),
                    'VALUE'       => $deliveryOption['value']
                ]);
            }

            $v->assign('TOTAL_COST', number_format__($totalCost + $deliveryCost));
        } else {
            $v->assign('TOTAL_COST', number_format__($totalCost));
        }

        $this->css();

        return $v;
    }

    private function getDeliveryOptionOperator($operator)
    {
        $operators = [
            'ADD'      => '+',
            'SUBTRACT' => '-',
            'MULTIPLY' => '*',
            'DIVIDE'   => '/'
        ];

        return $operators[$operator];
    }

    private function fixItemData($item)
    {
        aa($item, [
            'name'  => '',
            'props' => ''
        ]);

        return $item;
    }

    private function getPropsString($item)
    {
        if ($item['props']) {
            $props = $item['props'];

            $list = [];

            foreach ((array)$props as $prop) {
                if ($prop['label']) {
                    $list[] = $prop['label'] . ': ' . $prop['value'];
                } else {
                    $list[] = $prop['value'];
                }
            }

            return implode('; ', $list);
        }
    }
}
