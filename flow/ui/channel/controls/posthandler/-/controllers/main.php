<?php namespace ss\flow\ui\channel\controls\posthandler\controllers;

class Main extends \Controller
{
    private $channel;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->instance_($this->channel->id);
        } else {
            $this->lock();
        }

        if (!$this->a('ewma\dev:')) {
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
        $channelPack = pack_model($channel);
        $channelXPack = xpack_model($channel);

        $modulePath = 'customNodes/ss/flow/posthandlers';

        if (!$this->app->modules->getByPath($modulePath)) {
            $this->c('\ewma\dev~:createModule', [
                'path'  => $modulePath,
                'reset' => true
            ]);

            $this->app->modules->reload();
        }

        $targetNode = $modulePath . ' channel_' . $channel->id;

        $module = $this->app->modules->getByPath($modulePath);

        $v->assign([
                       'CP'     => $this->c('>cp:view', [
                           'channel' => $channel
                       ]),
                       'EDITOR' => $this->c('\ewma\nodeFileEditor~:view|ss/flow/' . $channel->id . '/posthandler', [
                           'node_type'                  => 'controller',
                           'target_node'                => $targetNode,
                           'template_node'              => $this->_p('data/codeTemplates/posthandler'),
                           'tokenize'                   => [
                               'NAMESPACE'  => $module->namespace . '\\controllers',
                               'CLASS_NAME' => 'Channel_' . $channel->id
                           ],
                           'callbacks'                  => [
                               'save'   => $this->_abs('>app:onSave', [
                                   'channel' => $channelPack
                               ]),
                               'reset'  => $this->_abs('>app:onReset', [
                                   'channel' => $channelPack
                               ]),
                               'update' => $this->_abs('>app:onUpdate', [
                                   'channel' => $channelPack
                               ])
                           ],
                           'resizable_closest_selector' => '.ui-dialog'
                       ])
                   ]);

        $this->css();

        $this->widget(':|', [
            '.e' => [
                'ss/flow/posthandler/channel_' . $channel->id . '/update' => 'mr.reload'
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
