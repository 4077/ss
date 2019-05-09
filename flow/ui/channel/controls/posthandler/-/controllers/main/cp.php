<?php namespace ss\flow\ui\channel\controls\posthandler\controllers\main;

class Cp extends \Controller
{
    private $channel;

    private $channelXPack;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

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

        $cache = $this->d('\ewma\nodeFileEditor~:cache|ss/flow/' . $channel->id . '/posthandler');

        if ($cache) {
            $v->assign([
                           'SAVE_BUTTON'  => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:save',
                               'data'    => [
                                   'channel' => $channelXPack
                               ],
                               'class'   => 'save_button',
                               'content' => 'save'
                           ]),
                           'RESET_BUTTON' => $this->c('\std\ui button:view', [
                               'ctrl'    => [
                                   'path' => '>xhr:reset',
                                   'data' => [
                                       'channel' => $channelXPack
                                   ]
                               ],
                               'class'   => 'reset_button',
                               'content' => 'reset'
                           ]),
                       ]);
        }

        $targetNode = 'customNodes/ss/flow/posthandlers channel_' . $channel->id;

        $v->assign([
                       'NODE_PATH' => $targetNode,
                       'NODE_URL'  => abs_url('cp/modules/node/?path=' . $targetNode . '&type=controller'),
                       'IDEA_URL'  => 'phpstorm://open/?file=' . abs_path($this->_nodeFilePath(force_l_slash($targetNode), 'controllers') . '.php' . '&line=1')
                   ]);

        $this->css();

        $this->c('\css\fontawesome~:load');

        $this->widget(':|', [
            '.e' => [
                'ss/flow/posthandler/channel_' . $channel->id . '/save'        => 'mr.reload',
                'ss/flow/posthandler/channel_' . $channel->id . '/reset'       => 'mr.reload',
                'ss/flow/posthandler/channel_' . $channel->id . '/update-self' => 'mr.reload',
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload', [
                    'channel' => $channelXPack
                ])
            ]
        ]);

        return $v;
    }
}
