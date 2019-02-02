<?php namespace ss\cp\orders\controllers\main\orders;

use ss\models\Order as OrderModel;

class Grid extends \Controller
{
    public function view()
    {
        $v = $this->v();

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\grid~:view|' . $this->_nodeId(), [
                           'set'      => [
                               'filter' => $this->getFilter()
                           ],
                           'defaults' => [
                               'model'   => OrderModel::class,
                               'filter'  => $this->getFilter(),
                               'pager'   => ['page' => 1, 'per_page' => 10],
                               'sorter'  => ['id' => 'DESC'],
                               'columns' => $this->getColumns()
                           ]
                       ])
                   ]);

        $this->css();

        return $v;
    }

    private function getFilter()
    {
        return [];
    }

    private function getColumns()
    {
        return [
            'id'             => [
                'label'    => '#',
                'sortable' => true,
            ],
            'time'           => [
                'field'    => 'created_at',
                'label'    => 'Время',
                'sortable' => true,
            ],
            'items'          => [
                'label'    => 'Товары',
                'sortable' => false,
                'width'    => '450, 450 -',
                'control'  => [
                    '>controls/table:view',
                    [
                        'order' => '%model'
                    ]
                ],
            ],
            'phone'          => [
                'label'    => 'Телефон',
                'sortable' => false,
                'field'    => 'client/phone'
            ],
            'fio'            => [
                'label'    => 'ФИО',
                'sortable' => false,
                'field'    => 'client/fio'
            ],
            'organization'   => [
                'label'    => 'Организация',
                'sortable' => false,
                'field'    => 'client/organization'
            ],
            'email'          => [
                'label'    => 'E-mail',
                'sortable' => false,
                'field'    => 'client/email'
            ],
            'address'        => [
                'label'    => 'Адрес',
                'sortable' => false,
                'field'    => 'client/address'
            ],
            'client_comment' => [
                'label'    => 'Комментарий',
                'sortable' => false,
                'field'    => 'client/comment'
            ],
            'actions'        => [
                'label'         => 'Действия',
                'label_visible' => false,
                'field'         => false,
                'control'       => [
                    '>controls/actions:view',
                    [
                        'order_id' => '%model_id'
                    ]
                ]
            ]
        ];
    }
}