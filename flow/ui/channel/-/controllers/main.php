<?php namespace ss\flow\ui\channel\controllers;

class Main extends \Controller
{
    private $channel;

    private $channelXPack;

    private $settings;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

            $this->settings = _j($this->channel->settings);

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

        $v->assign([
                       //
                       // trees
                       //
                       'SOURCE_TREE_SETTINGS'    => $this->c('>treeSettings:view', [
                           'channel' => $channel,
                           'type'    => 'source'
                       ]),
                       'TARGET_TREE_SETTINGS'    => $this->c('>treeSettings:view', [
                           'channel' => $channel,
                           'type'    => 'target'
                       ]),
                       //
                       // fields
                       //
                       'SOURCE_FIELD_SETTINGS'   => $this->c('>fieldSettings:view', [
                           'channel' => $channel,
                           'type'    => 'source'
                       ]),
                       'COLLATION_MODE_SELECTOR' => $this->collationModeSelectorView(),
                       'TARGET_FIELD_SETTINGS'   => $this->c('>fieldSettings:view', [
                           'channel' => $channel,
                           'type'    => 'target'
                       ])
                   ]);

        $this->assignConnectionsInfo($v);

        $this->assignUpdateCp($v);

        $this->css(':\css\std~');

        $collationXPid = false;
        if ($collationPid = $this->d('^~:xpids/collation')) {
            if ($process = $this->app->processDispatcher->open($collationPid)) {
                $collationXPid = $process->getXPid();
            } else {
                $this->d('^~:xpids/collation', false, RR);
            }
        }

        $updateXPid = false;
        if ($updatePid = $this->d('^~:xpids/update')) {
            if ($process = $this->app->processDispatcher->open($updatePid)) {
                $updateXPid = $process->getXPid();
            } else {
                $this->d('^~:xpids/update', false, RR);
            }
        }

        $this->widget(':|', [
            '.e'            => [
                'ss/flow/channel/settings/updateFilteringCats' => 'r.reload'
            ],
            '.payload'      => [
                'channel' => xpack_model($channel)
            ],
            '.r'            => [
                'collationInfo' => $this->_p('>xhr:collationInfo'),
                'collate'       => $this->_p('>xhr:collate'),
                'update'        => $this->_p('>xhr:update'),
                'reload'        => $this->_p('>xhr:reload')
            ],
            'channelId'     => $channel->id,
            'collationXPid' => $collationXPid,
            'updateXPid'    => $updateXPid
        ]);

        return $v;
    }

    //
    //
    //

    private function assignUpdateCp(\ewma\Views\View $v)
    {
        $v->assign('update_cp', [
            'POSTHANDLER_ENABLED_CLASS' => ap($this->settings, 'posthandler_enabled') ? 'enabled' : '',
            'POSTHANDLER_TOGGLE_BUTTON' => $this->posthandlerToggleButton(),
            'POSTHANDLER_EDIT_BUTTON'   => $this->posthandlerEditButton()
        ]);

        $selectedTab = &$this->s(':update_cp/selected_tab', 'price');

        $tabs = $this->getUpdateCpTabs();

        foreach ($tabs as $tab => $tabData) {
            $v->assign('update_cp/tab', [
                'BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:selectTransferCpTab',
                    'data'    => [
                        'tab'     => $tab,
                        'channel' => $this->channelXPack
                    ],
                    'class'   => 'tab ' . ($selectedTab == $tab ? 'selected' : ''),
                    'content' => $tabData['label']
                ])
            ]);
        }

        if ($selectedTabData = $tabs[$selectedTab] ?? false) {
            $v->append('update_cp', [
                'CONTENT' => $this->_call($selectedTabData['ui_call'])->ra(['channel' => $this->channel])->perform(),
                'CLASS'   => $selectedTabData['class'] ?? ''
            ]);
        }
    }

    private function getUpdateCpTabs()
    {
        $tabs = [
            'streams' => [
                'label'   => 'Данные подразделений',
                'ui_call' => $this->_abs('>streams:view'),
                'class'   => 'padding'
            ]
        ];

        return $tabs;
    }

    private function assignConnectionsInfo(\ewma\Views\View $v)
    {
        $connectionsCount = \ss\flow\models\ProductsConnection::where('channel_id', $this->channel->id)->count();

        $v->assign([
                       'CONNECTIONS_COUNT' => $connectionsCount ?: 'нет связей'
                   ]);
    }

    private function collationModeSelectorView()
    {
        return $this->c('\std\ui select:view', [
            'path'     => '>xhr:selectCollationMode',
            'data'     => [
                'channel' => $this->channelXPack
            ],
            'items'    => [
                'equal'      => '==',
                'like_left'  => '< like',
                'like_right' => '> like',
            ],
            'selected' => ap($this->settings, 'collation/mode')
        ]);
    }

    private function posthandlerToggleButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '>xhr:togglePosthandler',
            'data'  => [
                'channel' => $this->channelXPack
            ],
            'class' => 'toggle_button ',
            'icon'  => 'fa fa-power-off'
        ]);
    }

    private function posthandlerEditButton()
    {
        return $this->c('\std\ui button:view', [
            'path'    => '>xhr:editPosthandler',
            'data'    => [
                'channel' => $this->channelXPack
            ],
            'class'   => 'edit_button',
            'content' => 'постобработка'
        ]);
    }
}
