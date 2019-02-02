<?php namespace ss\cats\cp\page\controllers;

class Main extends \Controller
{
    private $cat;

    private $viewInstance;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
            $this->viewInstance = $this->cat->id;
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        if (ss()->cats->isEditable($this->cat)) {
            $v = $this->v('|' . $this->viewInstance);

            $s = $this->s(false, [
                'tab' => 'common'
            ]);

            $cat = $this->cat;
            $catXPack = xpack_model($cat);

            $tabs = $this->getTabs();
            $tabs += $this->getExtensionsTabs();

            if (count($tabs) > 1) {
                foreach ($tabs as $tab => $tabData) {
                    $v->assign('tab', [
                        'BUTTON' => $this->c('\std\ui button:view', [
                            'path'    => '>xhr:selectTab|',
                            'data'    => [
                                'tab' => $tab,
                                'cat' => $catXPack
                            ],
                            'class'   => 'tab ' . ($s['tab'] == $tab ? 'selected' : ''),
                            'content' => $tabData['label']
                        ])
                    ]);
                }
            }

            if ($selectedTab = $tabs[$s['tab']] ?? false) {
                $v->assign([
                               'CONTENT' => $this->_call($selectedTab['ui_call'])->ra(['cat' => $cat])->perform(),
                               'CLASS'   => $selectedTab['class'] ?? ''
                           ]);
            }

            $this->css();

            return $v;
        } else {
            return $this->c('\ss\cats\cp accessDenied:view');
        }
    }

    private function getTabs()
    {
        $cat = $this->cat;

        $tabs = [
            'common'     => [
                'label'   => 'Основное',
                'ui_call' => $this->_abs('common~:view|')
            ],
            'components' => [
                'label'   => 'Компоненты',
                'ui_call' => $this->_abs('<common/components~:view|', [
                    'type' => 'renderer'
                ]),
                'class'   => 'padding'
            ],
            'wrappers'   => [
                'label'   => 'Врапперы',
                'ui_call' => $this->_abs('<common/components~:view|', [
                    'type' => 'wrapper'
                ]),
                'class'   => 'padding'
            ],
            //            'data'     => [
            //                'label'   => 'data',
            //                'ui_call' => $this->_abs('<common/data~:view|')
            //            ],
            //            'handler'  => [
            //                'label'   => 'HANDLER',
            //                'ui_call' => $this->_abs('<common/handler~:view|')
            //            ],
            'less'       => [
                'label'   => 'LESS',
                'ui_call' => $this->_abs('<common/less~:view|')
            ]
        ];

        if (!$this->a('ewma:dev')) {
            $tabs = unmap($tabs, 'data, handler');
        }

        $lessEditable = $this->a('ss:cats/less');

        if (!$lessEditable) {
            $catIsOwn = ss()->own->isCatOwn($cat->tree_id, $cat);
            $ownLessEditable = $this->a('ss:cats/less/own');

            $lessEditable = $catIsOwn && $ownLessEditable;
        }

        if (!$lessEditable) {
            $tabs = unmap($tabs, 'less');
        }

        return $tabs;
    }

    private function getExtensionsTabs()
    {
        $extensions = [];

        $cat = $this->cat;

        $catComponentsPivots = ss()->cats->getComponentsPivots($cat, 'renderer');

        foreach ($catComponentsPivots as $pivot) {
            if ($component = $pivot->component) {
                if ($extensionsHandler = components()->getHandler($component, 'extensions')) {
                    $extension = handlers()->render($extensionsHandler, [
                        'cat' => $cat
                    ]);

                    ra($extensions, $extension);
                }
            }
        }

        return $extensions;
    }
}
