<?php namespace ss\flow\ui\channel\controllers\main\streams;

class Svc extends \Controller
{
    public $singleton = true;

    public function getStreamDefaultData()
    {
        return [
            'enabled'    => true,
            'source'     => false,
            'target'     => false,
            'data'       => [
                'price'    => [
                    'enabled'                => false,
                    'use_coefficients_table' => false,
                    'coefficient'            => 1
                ],
                'discount' => [
                    'enabled' => false
                ]
            ],
            'warehouses' => []
        ];
    }

    public function getWarehousesStreamDefaultData()
    {
        return [
            'enabled' => true,
            'id'      => false,
            'data'    => [
                'stock'    => [
                    'enabled' => true
                ],
                'reserved' => [
                    'enabled' => true
                ],
            ]
        ];
    }
}
