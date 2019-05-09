<?php namespace ss\flow\ui\channels\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateNodePosition()
    {
        $nodeId = $this->data('node_id');
        $left = $this->data('left');
        $top = $this->data('top');

        $this->d('~:nodes/positions/' . $nodeId, [
            'left' => $left,
            'top'  => $top
        ], RR);
    }

    public function openChannel()
    {
        if ($channel = \ss\flow\models\Channel::find($this->data('channel_id'))) {
            $this->c('\std\ui\dialogs~:open:channel, ss|ss/flow/channels', [
                'path'          => '^ui/channel~:view',
                'data'          => [
                    'channel' => pack_model($channel)
                ],
                'class'         => 'padding',
                'pluginOptions' => [
                    'title' => $channel->id
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 350,
                        'height' => 400
                    ]
                ]
            ]);
        }
    }

    public function createChannel()
    {
        $sourceId = $this->data('source_id');
        $targetId = $this->data('target_id');

        $source = \ss\flow\models\Node::find($sourceId);
        $target = \ss\flow\models\Node::find($targetId);

        if ($source && $target) {
            \ss\flow\models\Channel::create([
                                                'source_id' => $sourceId,
                                                'target_id' => $targetId,
                                                'settings'  => j_([
                                                                      'collation' => [
                                                                          'field' => [
                                                                              'source' => 'vendor_code',
                                                                              'target' => 'vendor_code'
                                                                          ],
                                                                          'mode'  => 'equal'
                                                                      ]
                                                                  ])
                                            ]);
        }
    }
}
