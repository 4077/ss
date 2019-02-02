<?php namespace ss\products\cp\controllers\app;

class Fields extends \Controller
{
    public function get($instance = '')
    {
        $dialogsContainerInstance = $this->data('dialogs_container_instance');

        $columns = [
            'id'          => [
                'label' => '#'
                //                'visible' => false,
            ],
            //            'cat'              => [
            //                'label'   => 'Категория',
            //                'width'   => '125, 300 -',
            //                'control' => [
            //                    '\ss/cp/productControls/catSelect~:view|' . $instance,
            //                    [
            //                        'product' => '%model'
            //                    ]
            //                ],
            //            ],
            'articul'     => [
                'label'    => 'Артикул',
                'width'    => '50, 100 -',
                'sortable' => true
            ],
            'name'        => [
                'label'    => 'Наименование',
                'width'    => '160, 160 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'name'
                    ]
                ]
            ],
            'remote_name' => [
                'label'    => 'Наименование (импорт)',
                'width'    => '40, 160 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'remote_name'
                    ]
                ]
            ],
            'stock'       => [
                'label'    => 'Кол-во',
                'width'    => '50, 100 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'stock'
                    ]
                ]
            ],
            'units'       => [
                'label'    => 'Ед. изм.',
                'width'    => '40, 100 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'units'
                    ]
                ]
            ],
            'price'       => [
                'label'    => 'Цена за ед. изм.',
                'width'    => '90, 100 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'price'
                    ]
                ]
            ],
            'unit_size'   => [
                'label'    => 'Кратность',
                'width'    => '40, 100 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'unit_size'
                    ]
                ]
            ],
            'alt_units'   => [
                'label'    => 'Доп. ед. изм.',
                'width'    => '40, 100 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'alt_units'
                    ]
                ]
            ],
            'alt_price'   => [
                'label'    => 'Цена за доп. ед. изм.',
                'width'    => '90, 100 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'alt_price'
                    ]
                ]
            ],
            'old_price'   => [
                'label'    => 'Старая цена',
                'width'    => '90, 100 -',
                'sortable' => true,
                'class'    => 'txt',
                'control'  => [
                    '\ss/cp/productControls/txt~:view',
                    [
                        'product' => '%model',
                        'field'   => 'old_price'
                    ]
                ]
            ],
            //            'import_unit_size' => [
            //                'label'    => 'Кратность (импорт)',
            //                'width'    => '40, 100 -',
            //                'sortable' => true,
            //                'class'    => 'txt',
            //                'control'  => [
            //                    '\ss/cp/productControls/txt~:view',
            //                    [
            //                        'product' => '%model',
            //                        'field'   => 'import_unit_size'
            //                    ]
            //                ]
            //            ],
            //            'import_units'     => [
            //                'label'    => 'Ед. изм. (импорт)',
            //                'width'    => '40, 100 -',
            //                'sortable' => true,
            //                'width'    => 60,
            //                'class'    => 'txt',
            //                'control'  => [
            //                    '\ss/cp/productControls/txt~:view',
            //                    [
            //                        'product' => '%model',
            //                        'field'   => 'import_units'
            //                    ]
            //                ]
            //            ],
            //            'delivery_info'    => [
            //                'label'    => 'Инф. о доставке',
            //                'field'    => false,
            //                'sortable' => false,
            //                'control'  => [
            //                    '\ss/cp/productControls/deliveryInfoSelect~:view|' . $instance,
            //                    [
            //                        'product' => '%model'
            //                    ]
            //                ],
            //            ],
            //            'delivery_k'       => [
            //                'label'    => 'k габаритности',
            //                'width'    => '40, 100 -',
            //                'sortable' => true,
            //                'class'    => 'txt',
            //                'control'  => [
            //                    '\ss/cp/productControls/txt~:view',
            //                    [
            //                        'product' => '%model',
            //                        'field'   => 'delivery_k'
            //                    ]
            //                ]
            //            ],
            'enabled'     => [
                'label'    => 'Видимый',
                'width'    => '40, 100 -',
                'sortable' => true,
                'class'    => 'toggle',
                'control'  => [
                    '\ss/cp/productControls/toggle~:view',
                    [
                        'product' => '%model',
                        'field'   => 'enabled'
                    ]
                ]
            ],
            'published'   => [
                'label'    => 'Опубликован',
                'width'    => '40, 100 -',
                'sortable' => true,
                'class'    => 'toggle',
                'control'  => [
                    '\ss/cp/productControls/toggle~:view',
                    [
                        'product' => '%model',
                        'field'   => 'published'
                    ]
                ]
            ],
            'props'       => [
                'label'    => 'Характеристики',
                'sortable' => false,
                'control'  => [
                    '\ss/cp/productControls/props~:view',
                    [
                        'product'                    => '%model',
                        'dialogs_container_instance' => $dialogsContainerInstance
                    ]
                ]
            ],
            //            'search_keywords'  => [
            //                'label'   => 'Ключевые слова',
            //                'class'   => 'txt',
            //                'control' => [
            //                    '\ss/cp/productControls/txt~:view',
            //                    [
            //                        'product' => '%model',
            //                        'field'   => 'search_keywords'
            //                    ]
            //                ]
            //            ],
            'images'      => [
                'label'    => 'Картинки',
                'sortable' => false,
                'control'  => [
                    '\ss/cp/productControls/images~:view',
                    [
                        'product'                    => '%model',
                        'dialogs_container_instance' => $dialogsContainerInstance
                    ]
                ]
            ],
            'actions'     => [
                'label'         => 'Действия',
                'label_visible' => false,
                'width'         => 99,
                'field'         => false,
                'control'       => [
                    '\ss/cp/productControls/actions~:view',
                    [
                        'product'                    => '%xpack',
                        'dialogs_container_instance' => $dialogsContainerInstance
                    ]
                ]
            ],
            'qr_code'     => [
                'label'   => 'QR-код',
                'field'   => false,
                'control' => [
                    '\ss/cp/productControls/qrCode~:view',
                    [
                        'product' => '%model'
                    ]
                ]
            ]
        ];

        return $columns;
    }
}
