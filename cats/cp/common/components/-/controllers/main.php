<?php namespace ss\cats\cp\common\components\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {

        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->cat->id)->replace($this->view());
    }

    public function view()
    {
        $cat = $this->cat;
        $catPack = pack_model($cat);
        $catXPack = xpack_model($cat);

        $v = $this->v('|' . $cat->id);

        $devAccess = $this->a('ewma\dev:');

        $catComponentsPivots = ss()->cats->getComponentsPivots($cat, $this->data('type'));

        $availableComponentsCatsIds = ss()->trees->getAvailableComponentsCatsIds($cat->tree_id, $cat->type, $this->data('type'));

        foreach ($catComponentsPivots as $pivot) {
            if ($component = $pivot->component) {
                $v->assign('component', [
                    'PIVOT_ID'             => $pivot->id,
                    'ENABLED_CLASS'        => $pivot->enabled ? 'enabled' : '',
                    'NAME'                 => components()->getFullName($component, $availableComponentsCatsIds),
                    'TOGGLE_BUTTON'        => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:toggle',
                        'data'  => [
                            'pivot' => xpack_model($pivot)
                        ],
                        'class' => 'toggle button',
                        'icon'  => 'fa fa-power-off'
                    ]),
                    'TOGGLE_PINNED_BUTTON' => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:togglePinned',
                        'data'  => [
                            'pivot' => xpack_model($pivot)
                        ],
                        'class' => 'toggle_pinned button ' . ($pivot->pinned ? 'pressed' : ''),
                        'icon'  => $pivot->pinned ? 'fa fa-star' : 'fa fa-star-o'
                    ]),
                    'PIVOT_DATA_BUTTON'    => $devAccess
                        ? $this->c('\std\ui button:view', [
                            'path'  => '>xhr:dataDialog|',
                            'data'  => [
                                'pivot' => xpack_model($pivot)
                            ],
                            'class' => 'pivot_data button',
                            'icon'  => 'fa fa-database'
                        ])
                        : '',
                    'DEV_CP_BUTTON'        => $devAccess
                        ? $this->c('\std\ui button:view', [
                            'path'  => '>xhr:componentDialog|',
                            'data'  => [
                                'pivot'    => xpack_model($pivot),
                                'instance' => j64_('dev-cp')
                            ],
                            'class' => 'dev_cp button',
                            'icon'  => 'fa fa-gear'
                        ])
                        : '',
                    'DELETE_BUTTON'        => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:delete',
                        'data'  => [
                            'pivot' => xpack_model($pivot)
                        ],
                        'class' => 'delete button',
                        'icon'  => 'fa fa-trash-o'
                    ])
                ]);

                $this->c('\std\ui button:bind', [
                    'selector' => $this->_selector('|' . $cat->id) . " .component[pivot_id='" . $pivot->id . "']",
                    'path'     => '>xhr:componentDialog|',
                    'data'     => [
                        'pivot'    => xpack_model($pivot),
                        'instance' => j64_('cp')
                    ]
                ]);
            }
        }

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('|' . $cat->id) . ' .components',
            'items_id_attr'  => 'pivot_id',
            'path'           => '>xhr:arrange',
            'data'           => [
                'cat' => $catXPack
            ],
            'plugin_options' => [
                'distance' => 20,
                'axis'     => 'y'
            ]
        ]);

        $v->assign([
                       'ADD_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:componentSelectorDialog|',
                           'data'    => [
                               'cat'  => $catXPack,
                               'type' => $this->data('type')
                           ],
                           'class'   => 'add_button',
                           'content' => 'Добавить'
                       ])
                   ]);

        $this->css(':\css\std~');

        $this->widget(':|' . $cat->id, [
            '.r'    => [
                'reload' => $this->_abs('>xhr:reload|', [
                    'cat'  => $catPack,
                    'type' => $this->data('type')
                ])
            ],
            'catId' => $cat->id
        ]);

        return $v;
    }
}
