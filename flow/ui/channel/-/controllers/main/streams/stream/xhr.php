<?php namespace ss\flow\ui\channel\controllers\main\streams\stream;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private $channel;

    private $channelSettings;

    private $streamNumber;

    private $streamData;

    /**
     * @var $svc \ss\flow\ui\channel\controllers\main\streams\Svc
     */
    private $svc;

    public function __create()
    {
        $this->channel = $this->unxpackModel('channel');
        $this->streamNumber = $this->data('stream_number');

        if ($this->channel && null !== $this->streamNumber) {
            $this->channelSettings = _j($this->channel->settings);

            $this->streamData = ap($this->channelSettings, 'streams/' . $this->streamNumber);

            $this->svc = $this->c('<<svc');
        } else {
            $this->lock();
        }
    }

    private function reload()
    {
        $this->c('<:reload', [
            'channel'       => $this->channel,
            'stream_number' => $this->streamNumber
        ]);
    }

    private function streamData($path, $value = null)
    {
        if (null === $value) {
            return ap($this->streamData, $path);
        } else {
            ap($this->streamData, $path, $value);
            ap($this->channelSettings, 'streams/' . $this->streamNumber, $this->streamData);

            $this->channel->settings = j_($this->channelSettings);
            $this->channel->save();
        }
    }

    //
    // stream
    //

    public function toggle()
    {
        $enabled = $this->streamData('enabled');

        $this->streamData('enabled', !$enabled);

        $this->reload();
    }

    public function selectDivision()
    {
        $type = $this->data('type');

        if (in($type, 'source, target')) {
            $this->streamData($type, $this->data('value'));

            $this->reload();
        }
    }

    //
    // data
    //

    // price, discount

    public function togglePrice()
    {
        $enabled = $this->streamData('data/price/enabled');

        $this->streamData('data/price/enabled', !$enabled);

        $this->reload();
    }

    public function toggleCoefficientsTableUsage()
    {
        $this->streamData('data/price/use_coefficients_table', (bool)$this->data('value'));

        $this->reload();
    }

    public function updatePriceCoefficient()
    {
        $txt = \std\ui\Txt::value($this);

        $value = $txt->value;

        $value = \ewma\Data\Formats\Numeric::parseDecimal($value);

        if (is_numeric($value)) {
            $this->streamData('data/price/coefficient', $value);
        } else {
            $txt->response($this->streamData('data/price/coefficient'));
        }

        $this->reload();
    }

    public function toggleDiscount()
    {
        $enabled = $this->streamData('data/discount/enabled');

        $this->streamData('data/discount/enabled', !$enabled);

        $this->reload();
    }

    //
    // warehouses
    //

    public function createWarehousesStream()
    {
        $warehouses = $this->streamData('warehouses');

        $warehouses[] = $this->svc->getWarehousesStreamDefaultData();

        $this->streamData('warehouses', $warehouses);

        $this->reload();
    }

    public function toggleWarehousesStream()
    {
        $warehousesStreamNumber = $this->data('warehouses_stream_number');

        if ($warehousesStream = $this->streamData('warehouses/' . $warehousesStreamNumber)) {
            $enabled = &ap($warehousesStream, 'enabled');

            invert($enabled);

            $this->streamData('warehouses/' . $warehousesStreamNumber, $warehousesStream);

            $this->reload();
        }
    }

    public function selectWarehouse()
    {
        $sourceDivisionId = $this->streamData('source');

        $warehouseId = $this->data('value');

        $warehousesStreamNumber = $this->data('warehouses_stream_number');

        if (!$warehouseId) {
            $this->streamData('warehouses/' . $warehousesStreamNumber . '/id', false);
        }

        if ($warehouse = ss()->multisource->getWarehouse($warehouseId)) {
            if ($warehouse->division_id == $sourceDivisionId) {
                $this->streamData('warehouses/' . $warehousesStreamNumber . '/id', $warehouseId);
            }
        }

        $this->reload();
    }

    public function deleteWarehousesStream()
    {
        $warehousesStreamNumber = $this->data('warehouses_stream_number');

        $warehouses = $this->streamData('warehouses');

        if (isset($warehouses[$warehousesStreamNumber])) {
            if ($this->data('discarded')) {
                $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/flow/channels');
            } else {
                if ($this->data('confirmed')) {
                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/flow/channels');

                    unset($warehouses[$warehousesStreamNumber]);

                    $warehouses = array_values($warehouses);

                    $this->streamData('warehouses', $warehouses);
                    $this->reload();
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm, ss|ss/flow/channels', [
                        'path'  => '\std dialogs/confirm~:view',
                        'data'  => [
                            'confirm_call' => $this->_abs(':deleteWarehousesStream', $this->data),
                            'discard_call' => $this->_abs(':deleteWarehousesStream', $this->data),
                            'message'      => 'Удалить?'
                        ],
                        'class' => 'padding'
                    ]);
                }
            }
        }

        $this->reload();
    }

    // stock, reserved

    public function toggleStock()
    {
        $warehousesStreamNumber = $this->data('warehouses_stream_number');

        $enabled = $this->streamData('warehouses/' . $warehousesStreamNumber . '/data/stock/enabled');

        $this->streamData('warehouses/' . $warehousesStreamNumber . '/data/stock/enabled', !$enabled);

        $this->reload();
    }

    public function toggleReserved()
    {
        $warehousesStreamNumber = $this->data('warehouses_stream_number');

        $enabled = $this->streamData('warehouses/' . $warehousesStreamNumber . '/data/reserved/enabled');

        $this->streamData('warehouses/' . $warehousesStreamNumber . '/data/reserved/enabled', !$enabled);

        $this->reload();
    }
}
