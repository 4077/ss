<?php namespace ss\flow\ui\channel\controls\fieldPreprocessor\controllers;

// todo update-self/others events

class Main extends \Controller
{
    private $channel;

    private $type;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->type = $this->data('type');

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

        $type = $this->type;

        $modulePath = 'customNodes/ss/flow/fieldsPreprocessors';

        if (!$this->app->modules->getByPath($modulePath)) {
            $this->c('\ewma\dev~:createModule', [
                'path'  => $modulePath,
                'reset' => true
            ]);

            $this->app->modules->reload();
        }

        $targetNode = $modulePath . ' channel_' . $channel->id . '/' . $type;

        $module = $this->app->modules->getByPath($modulePath);

        $v->assign([
                       'CP'     => $this->c('>cp:view', [
                           'channel' => $channel,
                           'type'    => $type
                       ]),
                       'EDITOR' => $this->c('\ewma\nodeFileEditor~:view|ss/flow/' . $channel->id . '/fieldPreprocessor/' . $type, [
                           'node_type'                  => 'controller',
                           'target_node'                => $targetNode,
                           'template_node'              => $this->_p('data/codeTemplates/fieldPreprocessor'),
                           'tokenize'                   => [
                               'NAMESPACE'  => $module->namespace . '\\controllers\\channel_' . $channel->id,
                               'CLASS_NAME' => ucfirst($type)
                           ],
                           'callbacks'                  => [
                               'save'   => $this->_abs('>app:onSave', [
                                   'channel' => $channelPack,
                                   'type'    => $type
                               ]),
                               'reset'  => $this->_abs('>app:onReset', [
                                   'channel' => $channelPack,
                                   'type'    => $type
                               ]),
                               'update' => $this->_abs('>app:onUpdate', [
                                   'channel' => $channelPack,
                                   'type'    => $type
                               ])
                           ],
                           'resizable_closest_selector' => '.ui-dialog'
                       ])
                   ]);

        $this->css();

        $this->widget(':|', [
            '.e' => [
                'ss/flow/fieldPreprocessor/channel_' . $channel->id . '/' . $type . '/update' => 'mr.reload'
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
