<?php namespace ss\products\treesConnection\controllers;

class Main extends \Controller
{
    public function __create()
    {
        $this->packModels();
        $this->dmap('|', 'connection, adapter, direction');
        $this->unpackModels();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $connection = $this->data('connection');
        $direction = $this->data('direction');
        $adapter = $this->data('adapter');

        $data = ss()->trees->connections->adapterData($connection, $adapter, $direction);

        $buttons = $this->getButtons();

        foreach ($buttons as $field => $button) {
//            $type = $button['type'] ?? 'field';

            $v->assign('button', [
                'CONTENT' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:toggle|',
                    'data'    => [
                        'field' => $field
                    ],
                    'class'   => 'button ' . (ap($data, $field) ? 'pressed' : ''),
                    'content' => $button['label']
                ])
            ]);
        }

        $v->assign([
                       'CLASS' => $this->data('direction')
                   ]);

        $this->css();

        return $v;
    }

    private function getButtons()
    {
        return [
            'enabled'           => [
                'label' => 'Включен'
            ],
            'published'         => [
                'label' => 'Опубликован'
            ],
            'name'              => [
                'label' => 'Наименование'
            ],
            'remote_name'       => [
                'label' => 'Оригинальное наименование'
            ],
            'remote_short_name' => [
                'label' => 'Оригинальное короткое наименование'
            ],
            'receipt_date'      => [
                'label' => 'Дата поступления'
            ],
            'stock'             => [
                'label' => 'Наличие'
            ],
            'reserved'          => [
                'label' => 'Резерв'
            ],
            'price'             => [
                'label' => 'Цена'
            ],
            'discount'          => [
                'label' => 'Скидка'
            ],
            'units'             => [
                'label' => 'Ед. изм'
            ],
            'unit_size'         => [
                'label' => 'Кратность'
            ],
            'alt_price'         => [
                'label' => 'Цена за доп. ед. изм.'
            ],
            'alt_units'         => [
                'label' => 'Доп. ед. изм'
            ],
            'old_price'         => [
                'label' => 'Старая цена'
            ],
            'props'             => [
                'label' => 'Характеристики'
            ],
            'images'            => [
                'label' => 'Картинки',
                //                'type'  => 'relation'
            ]
        ];
    }
}
