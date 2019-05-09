<?php namespace ss\flow\ui\channel\controls\fieldPreprocessor\controllers\main;

class Cp extends \Controller
{
    private $channel;

    private $channelXPack;

    private $type;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

            $this->type = $this->data('type');

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
        $channelXPack = $this->channelXPack;

        $type = $this->type;

        $cache = $this->d('\ewma\nodeFileEditor~:cache|ss/flow/' . $channel->id . '/fieldPreprocessor/' . $type);

        if ($cache) {
            $v->assign([
                           'SAVE_BUTTON'  => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:save',
                               'data'    => [
                                   'channel' => $channelXPack,
                                   'type'    => $type
                               ],
                               'class'   => 'save_button',
                               'content' => 'save'
                           ]),
                           'RESET_BUTTON' => $this->c('\std\ui button:view', [
                               'ctrl'    => [
                                   'path' => '>xhr:reset',
                                   'data' => [
                                       'channel' => $channelXPack,
                                       'type'    => $type
                                   ]
                               ],
                               'class'   => 'reset_button',
                               'content' => 'reset'
                           ]),
                       ]);
        }

        $targetNode = 'customNodes/ss/flow/fieldsPreprocessors channel_' . $channel->id . '/' . $type;

        $v->assign([
                       'NODE_PATH' => $targetNode,
                       'NODE_URL'  => abs_url('cp/modules/node/?path=' . $targetNode . '&type=controller'),
                       'IDEA_URL'  => 'phpstorm://open/?file=' . abs_path($this->_nodeFilePath(force_l_slash($targetNode), 'controllers') . '.php' . '&line=1')
                   ]);

        $this->css();

        $this->c('\css\fontawesome~:load');

        $this->widget(':|', [
            '.e' => [
                'ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/save'        => 'mr.reload',
                'ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/reset'       => 'mr.reload',
                'ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/update-self' => 'mr.reload',
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload', [
                    'channel' => $channelXPack,
                    'type'    => $type
                ])
            ]
        ]);

        return $v;
    }
}
