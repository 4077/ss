<?php namespace ss\flow\ui\channel\controllers\main;

class Streams extends \Controller
{
    private $channel;

    private $channelXPack;

    private $settings;

    private $streams;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

            $this->settings = _j($this->channel->settings);
            $this->streams = ap($this->settings, 'streams') ?? [];

            $this->instance_($this->channel->id);
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

        $channel = $this->channel;
        $streams = $this->streams;

        foreach ($streams as $streamNumber => $stream) {
            $v->assign('stream', [
                'CONTENT' => $this->c('>stream:view', [
                    'channel'       => $channel,
                    'stream_number' => $streamNumber
                ])
            ]);
        }

        $v->assign([
                       'CREATE_STREAM_BUTTON' => $this->createStreamButton()
                   ]);

        $this->css();

        return $v;
    }

    private function createStreamButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:create',
            'data'  => [
                'channel' => $this->channelXPack
            ],
            'class' => 'create_stream_button',
            'icon'  => 'fa fa-plus'
        ]);
    }
}
