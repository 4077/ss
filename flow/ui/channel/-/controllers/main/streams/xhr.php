<?php namespace ss\flow\ui\channel\controllers\main\streams;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private $channel;

    private $channelSettings;

    private $streamsData;

    /**
     * @var $svc \ss\flow\ui\channel\controllers\main\streams\Svc
     */
    private $svc;

    public function __create()
    {
        $this->channel = $this->unxpackModel('channel');

        if ($this->channel) {
            $this->channelSettings = _j($this->channel->settings);

            $this->streamsData = ap($this->channelSettings, 'streams');

            $this->svc = $this->c('@svc');
        } else {
            $this->lock();
        }
    }

    private function update()
    {
        ap($this->channelSettings, 'streams', $this->streamsData);

        $this->channel->settings = j_($this->channelSettings);
        $this->channel->save();
    }

    private function reload()
    {
        $this->c('<:reload', [
            'channel' => $this->channel
        ]);
    }

    //
    //
    //

    public function create()
    {
        $this->streamsData[] = $this->svc->getStreamDefaultData();

        $this->update();
        $this->reload();
    }

    public function delete()
    {
        $streamNumber = $this->data('stream_number');

        if (isset($this->streamsData[$streamNumber])) {
            if ($this->data('discarded')) {
                $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/flow/channels');
            } else {
                if ($this->data('confirmed')) {
                    $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/flow/channels');

                    unset($this->streamsData[$streamNumber]);

                    $this->streamsData = array_values($this->streamsData);

                    $this->update();
                    $this->reload();
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm, ss|ss/flow/channels', [
                        'path'  => '\std dialogs/confirm~:view',
                        'data'  => [
                            'confirm_call' => $this->_abs(':delete', $this->data),
                            'discard_call' => $this->_abs(':delete', $this->data),
                            'message'      => 'Удалить?'
                        ],
                        'class' => 'padding'
                    ]);
                }
            }
        }

        $this->reload();
    }
}
