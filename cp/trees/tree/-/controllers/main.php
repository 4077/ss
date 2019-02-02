<?php namespace ss\cp\trees\tree\controllers;

class Main extends \Controller
{
    private $tree;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        pusher()->subscribe();

        $s = $this->s(false, [
            'tab' => 'common'
        ]);

        $tree = $this->tree;
        $treeXPack = xpack_model($tree);

        $tabs = $this->getTabs();

        if (count($tabs) > 1) {
            foreach ($tabs as $tab => $tabData) {
                $v->assign('tab', [
                    'BUTTON' => $this->c('\std\ui button:view', [
                        'path'    => '>xhr:selectTab|',
                        'data'    => [
                            'tab'  => $tab,
                            'tree' => $treeXPack
                        ],
                        'class'   => 'tab ' . ($s['tab'] == $tab ? 'selected' : ''),
                        'content' => $tabData['label']
                    ])
                ]);
            }
        }

        if ($selectedTab = $tabs[$s['tab']] ?? false) {
            $v->assign([
                           'CONTENT' => $this->_call($selectedTab['ui_call'])->ra(['tree' => $tree])->perform(),
                           'CLASS'   => $selectedTab['class'] ?? ''
                       ]);
        }

        $this->css();

        return $v;
    }

    private function getTabs()
    {
        $tabs = [
            'trees'       => [
                'label'   => 'Категории',
                'ui_call' => $this->_abs('cats~:view|')
            ],
            'components'  => [
                'label'   => 'Компоненты',
                'ui_call' => $this->_abs('components~:view|', [
                    'type' => 'renderer'
                ])
            ],
            'wrappers'    => [
                'label'   => 'Врапперы',
                'ui_call' => $this->_abs('components~:view|', [
                    'type' => 'wrapper'
                ])
            ],
            'settings'    => [
                'label'   => 'Настройки',
                'ui_call' => $this->_abs('settings~:view|')
            ],
            'plugins'     => [
                'label'   => 'Плагины',
                'ui_call' => $this->_abs('plugins~:view|')
            ],
            'connections' => [
                'label'   => 'Связи',
                'ui_call' => $this->_abs('<connections~:view', [
                    'select_tree_id' => $this->tree->id
                ])
            ],
            'own'         => [
                'label'   => 'Доступ',
                'ui_call' => $this->_abs('own~:view|')
            ]
        ];

        return $tabs;
    }
}
