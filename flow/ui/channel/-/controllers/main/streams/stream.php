<?php namespace ss\flow\ui\channel\controllers\main\streams;

class Stream extends \Controller
{
    private $channel;

    private $channelXPack;

    private $settings;

    private $streamNumber;

    private $stream;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

            $this->settings = _j($this->channel->settings);

            $this->streamNumber = $this->data('stream_number');
            $this->stream = ap($this->settings, 'streams/' . $this->streamNumber) ?? [];

            $this->instance_($this->channel->id . '/' . $this->streamNumber);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'ENABLED_CLASS'            => ap($this->stream, 'enabled') ? 'enabled' : '',
                       'TOGGLE_BUTTON'            => $this->toggleButton(),
                       'SOURCE_DIVISION_SELECTOR' => $this->divisionSelector('source'),
                       'TARGET_DIVISION_SELECTOR' => $this->divisionSelector('target'),
                       'DELETE_BUTTON'            => $this->deleteButton()
                   ]);

        $sourceDivision = ap($this->stream, 'source');
        $targetDivision = ap($this->stream, 'target');

        if ($sourceDivision && $targetDivision) {
            $v->assign('data', [
                'PRICE_ENABLED_CLASS'                    => ap($this->stream, 'data/price/enabled') ? 'enabled' : '',
                'DISCOUNT_ENABLED_CLASS'                 => ap($this->stream, 'data/discount/enabled') ? 'enabled' : '',
                'TOGGLE_PRICE_BUTTON'                    => $this->togglePriceButton(),
                'TOGGLE_DISCOUNT_BUTTON'                 => $this->toggleDiscountButton(),
                'PRICE_USE_INTERSECTIONS_TABLE_SELECTOR' => $this->usePriceCoefficientsTableSelector(),
                'CREATE_WAREHOUSES_STREAM_BUTTON'        => $this->createWarehousesStreamButton()
            ]);

            if (ap($this->stream, 'data/price/use_coefficients_table')) {
                if ($intersection = ss()->multisource->divisionsIntersections->getIntersectionByIds($this->stream['source'], $this->stream['target'])) {
                    $v->assign('data/price_coefficients_table_value', [
                        'VALUE' => $intersection->price_coefficient
                    ]);
                } else {
                    $v->assign('data/price_coefficients_table_value', [
                        'DOES_NOT_HAVE_CLASS' => 'does_not_have',
                        'VALUE'               => 1
                    ]);
                }
            } else {
                $v->assign('data/price_coefficient_manual_value', [
                    'TXT' => $this->c('\std\ui txt:view', [
                        'path'              => '>xhr:updatePriceCoefficient',
                        'data'              => [
                            'channel'       => $this->channelXPack,
                            'stream_number' => $this->streamNumber
                        ],
                        'fitInputToClosest' => '.manual_value',
                        'content'           => ap($this->stream, 'data/price/coefficient')
                    ])
                ]);
            }

            $warehouses = ap($this->stream, 'warehouses');

            foreach ($warehouses as $warehousesStreamNumber => $warehousesStreamData) {
                $v->assign('warehouse', [
                    'ENABLED_CLASS' => ap($warehousesStreamData, 'enabled') ? 'enabled' : '',
                    'TOGGLE_BUTTON' => $this->toggleWarehousesStreamButton($warehousesStreamNumber),
                    'SELECTOR'      => $this->warehouseSelectorView($warehousesStreamNumber),
                    'DELETE_BUTTON' => $this->deleteWarehousesStreamButton($warehousesStreamNumber)
                ]);

                $v->assign('warehouse/data', [
                    'STOCK_ENABLED_CLASS'    => ap($this->stream, 'warehouses/' . $warehousesStreamNumber . '/data/stock/enabled') ? 'enabled' : '',
                    'RESERVED_ENABLED_CLASS' => ap($this->stream, 'warehouses/' . $warehousesStreamNumber . '/data/reserved/enabled') ? 'enabled' : '',
                    'TOGGLE_STOCK_BUTTON'    => $this->toggleStockButton($warehousesStreamNumber),
                    'TOGGLE_RESERVED_BUTTON' => $this->toggleReservedButton($warehousesStreamNumber),
                ]);
            }
        }

        $this->css();

        return $v;
    }

    //
    // stream
    //

    private function toggleButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:toggle',
            'data'  => [
                'channel'       => $this->channelXPack,
                'stream_number' => $this->streamNumber
            ],
            'class' => 'toggle_button',
            'icon'  => 'fa fa-power-off'
        ]);
    }

    private function divisionSelector($type)
    {
        $items = [0 => ''] + table_cells_by_id(ss()->multisource->getDivisions(), 'name');

        $selectedDivisionId = ap($this->stream, $type);

        $warehousesStreams = ap($this->stream, 'warehouses');

        $locked = false;

        if ($type == 'source') {
            foreach ($warehousesStreams as $warehousesStream) {
                if (ap($warehousesStream, 'id')) {
                    $locked = true;

                    break;
                }
            }
        }

        if ($locked) {
            $division = ss()->multisource->getDivision($selectedDivisionId);

            return $this->c('\std\ui tag:view', [
                'attrs'   => [
                    'class' => 'locked'
                ],
                'content' => $division->name
            ]);
        } else {
            return $this->c('\std\ui select:view', [
                'path'     => '>xhr:selectDivision',
                'data'     => [
                    'channel'       => $this->channelXPack,
                    'type'          => $type,
                    'stream_number' => $this->streamNumber
                ],
                'items'    => $items,
                'selected' => $selectedDivisionId
            ]);
        }
    }

    private function deleteButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '@xhr:delete',
            'data'  => [
                'channel'       => $this->channelXPack,
                'stream_number' => $this->streamNumber
            ],
            'class' => 'delete_button',
            'icon'  => 'fa fa-trash-o'
        ]);
    }

    //
    // data
    //

    // price, discount

    private function togglePriceButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:togglePrice',
            'data'  => [
                'channel'       => $this->channelXPack,
                'stream_number' => $this->streamNumber
            ],
            'class' => 'toggle_button',
            'label' => 'Цена'
        ]);
    }

    private function usePriceCoefficientsTableSelector()
    {
        $items = [
            1 => 'По таблице',
            0 => 'Вручную'
        ];

        return $this->c('\std\ui select:view', [
            'path'     => '>xhr:toggleCoefficientsTableUsage',
            'data'     => [
                'channel'       => $this->channelXPack,
                'stream_number' => $this->streamNumber
            ],
            'items'    => $items,
            'selected' => (int)ap($this->stream, 'data/price/use_coefficients_table')
        ]);
    }

    private function toggleDiscountButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:toggleDiscount',
            'data'  => [
                'channel'       => $this->channelXPack,
                'stream_number' => $this->streamNumber
            ],
            'class' => 'toggle_button',
            'label' => 'Скидка'
        ]);
    }

    //
    // warehouses
    //

    private function createWarehousesStreamButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:createWarehousesStream',
            'data'  => [
                'channel'       => $this->channelXPack,
                'stream_number' => $this->streamNumber
            ],
            'class' => 'create_warehouses_stream_button',
            'icon'  => 'fa fa-plus'
        ]);
    }

    private function toggleWarehousesStreamButton($streamNumber)
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:toggleWarehousesStream',
            'data'  => [
                'channel'                  => $this->channelXPack,
                'stream_number'            => $this->streamNumber,
                'warehouses_stream_number' => $streamNumber
            ],
            'class' => 'toggle_button',
            'icon'  => 'fa fa-power-off'
        ]);
    }

    private function warehouseSelectorView($streamNumber)
    {
        $divisionId = ap($this->stream, 'source');

        if ($division = ss()->multisource->getDivision($divisionId)) {
            $warehouses = ss()->multisource->getDivisionWarehouses($divisionId);

            $items = [0 => ''] + table_cells_by_id($warehouses, 'name');

            $selected = ap($this->stream, 'warehouses/' . $streamNumber . '/id');

            return $this->c('\std\ui select:view', [
                'path'     => '>xhr:selectWarehouse',
                'data'     => [
                    'channel'                  => $this->channelXPack,
                    'stream_number'            => $this->streamNumber,
                    'warehouses_stream_number' => $streamNumber
                ],
                'items'    => $items,
                'selected' => $selected
            ]);
        }
    }

    private function deleteWarehousesStreamButton($streamNumber)
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:deleteWarehousesStream',
            'data'  => [
                'channel'                  => $this->channelXPack,
                'stream_number'            => $this->streamNumber,
                'warehouses_stream_number' => $streamNumber
            ],
            'class' => 'delete_button',
            'icon'  => 'fa fa-trash-o'
        ]);
    }

    // price, discount

    private function toggleStockButton($streamNumber)
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:toggleStock',
            'data'  => [
                'channel'                  => $this->channelXPack,
                'stream_number'            => $this->streamNumber,
                'warehouses_stream_number' => $streamNumber
            ],
            'class' => 'toggle_button',
            'label' => 'Наличие'
        ]);
    }

    private function toggleReservedButton($streamNumber)
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:toggleReserved',
            'data'  => [
                'channel'                  => $this->channelXPack,
                'stream_number'            => $this->streamNumber,
                'warehouses_stream_number' => $streamNumber
            ],
            'class' => 'toggle_button',
            'label' => 'Резерв'
        ]);
    }
}
