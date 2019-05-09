<?php namespace ss\flow\ui\channels\controllers;

class Main extends \Controller
{
    private $d;

    public function __create()
    {
        $this->d = $this->d(false, [
            'nodes' => [
                'positions' => []
            ]
        ]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $d = $this->d;

        $nodes = \ss\flow\models\Node::all();

        $nodesWidgetData = [];
        foreach ($nodes as $node) {
            $nodesWidgetData[$node->id] = [
                'position' => ap($d, 'nodes/positions/' . $node->id) ?: [
                    'left' => 0,
                    'top'  => 0
                ]
            ];

            $v->assign('node', [
                'ID'      => $node->id,
                'CONTENT' => $this->c('>node:view', [
                    'node' => $node
                ])
            ]);
        }

        $channels = \ss\flow\models\Channel::all();

        $channelsWidgetData = [];
        foreach ($channels as $channel) {
            $channelsWidgetData[$channel->id] = [$channel->source_id, $channel->target_id];
        }

        $v->assign([
                       'CONTEXTMENU'         => $this->c('>contextmenu:view'),
                       'NODE_CONTEXTMENU'    => $this->c('>nodeContextmenu:view'),
                       'CHANNEL_CONTEXTMENU' => $this->c('>channelContextmenu:view')
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:ss/flow/channels');

        $this->css();

        $this->c('\js\paper~:load');

        $this->widget(':|', [
            '.r'       => [
                'updateNodePosition' => $this->_p('>xhr:updateNodePosition'),
                'openChannel'        => $this->_p('>xhr:openChannel'),
                'createChannel'      => $this->_p('>xhr:createChannel')
            ],
            '.w'       => [
                'nodeContextmenu'    => $this->_w('>nodeContextmenu:'),
                'channelContextmenu' => $this->_w('>channelContextmenu:')
            ],
            'nodes'    => $nodesWidgetData,
            'channels' => $channelsWidgetData
        ]);

        return $v;
    }
}
