<?php namespace ss\flow\ui\channel\controllers\main;

class TreeSettings extends \Controller
{
    private $channel;

    private $channelXPack;

    private $type;

    private $settings;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

            $this->settings = _j($this->channel->settings);

            $this->type = $this->data('type');

            $this->instance_($this->channel->id . '/' . $this->type);
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

        $node = $channel->{$this->type};
        $tree = $node->tree;

        $v->assign([
                       'MODE_ICON'      => 'fa fa-' . ($tree->mode == 'folders' ? 'folder' : 'file'),
                       'NAME'           => $tree->name,
                       //
                       'ADD_CAT_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '~xhr:addFilteringCat',
                           'data'    => [
                               'channel' => $this->channelXPack,
                               'type'    => $this->type
                           ],
                           'class'   => 'add_filtering_cat_button',
                           'content' => 'Добавить категорию'
                       ]),
                   ]);

        if ($this->type == 'target') {
            $v->assign('toggle_connections_button', [
                'ENABLED_CLASS' => ap($this->settings, 'use_target_connections') ? 'enabled' : ''
            ]);

            $this->bindToggleConnectionsButton();
        }

        $this->assignCats($v);

        $this->css(':\css\std~');

        $this->widget(':|', [
            '.e' => [
                'ss/flow/channel/settings/updateFilteringCats/' . $this->type => 'r.reload'
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload', [
                    'channel' => xpack_model($channel)
                ])
            ]
        ]);

        return $v;
    }

    private function assignCats(\ewma\Views\View $v)
    {
        $catsIds = ap($this->settings, 'cats/' . $this->type) ?? [];

        $cats = \ss\models\Cat::whereIn('id', $catsIds)->get();

        foreach ($cats as $cat) {
            $nameBranch = ss()->cats->getNamesBranch($cat, false);

            array_shift($nameBranch);

            $v->assign('cat', [
                'NAME'          => '└ ' . implode('/', $nameBranch),
                'REMOVE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'  => '~xhr:removeFilteringCat',
                    'data'  => [
                        'channel' => $this->channelXPack,
                        'cat'     => xpack_model($cat),
                        'type'    => $this->type
                    ],
                    'class' => 'remove_button',
                    'icon'  => 'fa fa-close'
                ])
            ]);
        }
    }

    private function bindToggleConnectionsButton()
    {
        return $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|') . ' .toggle_connections_button',
            'path'     => '~xhr:toggleConnectionsUsing',
            'data'     => [
                'channel' => $this->channelXPack
            ]
        ]);
    }
}
