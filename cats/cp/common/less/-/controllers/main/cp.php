<?php namespace ss\cats\cp\common\less\controllers\main;

class Cp extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
            $this->instance_($this->cat->id);
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
        $s = &$this->s('~');

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $lessTypes = ss()->cats->getLessTypes($cat->type);

        $selectedLessType = ap($s, 'selected_less_type_by_cat_type/' . $cat->type);

        $less = ss()->cats->getLess($cat);

        $hasCached = false;
        $selectedCached = false;

        foreach ($lessTypes as $type) {
            $cache = $this->d('\ewma\nodeFileEditor~:cache|ss/cats/' . $cat->id . '/less/' . $type);

            if ($cache) {
                $hasCached = true;

                if ($type == $selectedLessType) {
                    $selectedCached = true;
                }
            }

            $lessData = $less[$type];

            if ($lessData['inheritable']) {
                $icon = $lessData['enabled'] ? ($lessData['rewrite'] ? 'fa fa-circle' : 'fa fa-arrow-right') : false;
            } else {
                $icon = $lessData['enabled'] ? 'fa fa-circle' : false;
            }

            if (count($lessTypes) > 1) {
                $v->assign('type', [
                    'BUTTON' => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:selectLessType',
                        'data'  => [
                            'cat'  => $catXPack,
                            'type' => $type
                        ],
                        'class' => 'type ' . ($selectedLessType == $type ? 'selected' : ''),
                        'icon'  => $icon,
                        'label' => $type . ($cache ? '*' : '')
                    ])
                ]);
            }
        }

        if (count($lessTypes) > 1) {
            $v->assign('row_1');
        }

        if ($hasCached) {
            $v->assign([
                           'SAVE_ALL_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:saveAll',
                               'data'    => [
                                   'cat' => $catXPack
                               ],
                               'class'   => 'save_button',
                               'content' => 'save'
                           ])
                       ]);
        }

        if ($selectedCached) {
            $v->assign([
                           'RESET_BUTTON' => $this->c('\std\ui button:view', [
                               'ctrl'    => [
                                   'path' => '>xhr:reset',
                                   'data' => [
                                       'cat' => $catXPack
                                   ]
                               ],
                               'class'   => 'reset_button',
                               'content' => 'reset'
                           ]),
                           'SAVE_BUTTON'  => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:save',
                               'data'    => [
                                   'cat' => $catXPack
                               ],
                               'class'   => 'save_button',
                               'content' => 'save'
                           ])
                       ]);
        }

        $v->assign([
                       'UPDATE_CSS_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:updateCss',
                           'data'  => [
                               'cat' => $catXPack
                           ],
                           'class' => 'update_css_button',
                           'icon'  => 'fa fa-refresh'
                       ])
                   ]);

        $lessData = $less[$selectedLessType];

        $enabled = $lessData['enabled'];

        $v->assign([
                       'ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => [
                               'type' => $selectedLessType,
                               'cat'  => $catXPack
                           ],
                           'class'   => 'toggle_button ' . ($enabled ? 'enabled' : ''),
                           'content' => $enabled ? 'enabled' : 'disabled'
                       ])
                   ]);

        if ($lessData['inheritable']) {
            $rewrite = $lessData['rewrite'];

            $v->assign([
                           'REWRITE_BUTTON' => $this->c('\std\ui button:view', [
                               'visible' => $enabled,
                               'path'    => '>xhr:toggleRewrite',
                               'data'    => [
                                   'type' => $selectedLessType,
                                   'cat'  => $catXPack
                               ],
                               'class'   => 'rewrite_button ' . ($rewrite ? 'enabled' : ''),
                               'content' => $rewrite ? 'rewrite' : 'extend'
                           ])
                       ]);
        }

        if ($this->a('ewma\dev:')) {
            $targetNode = 'customNodes/ss/cats/less cat_' . $cat->id . '/' . $selectedLessType;

            $v->assign('dev', [
                'NODE_PATH' => $targetNode,
                'NODE_URL'  => abs_url('cp/modules/node/?path=' . $targetNode . '&type=less'),
                'IDEA_URL'  => 'phpstorm://open/?file=' . abs_path($this->_nodeFilePath(force_l_slash($targetNode), 'less') . '.less' . '&line=1')
            ]);
        }

        $this->css();

        $this->c('\css\fontawesome~:load');

        $this->widget(':|', [
            '.e' => [
                'ss/cat/' . $cat->id . '/less/toggle_enabled' => 'mr.reload',
                'ss/cat/' . $cat->id . '/less/toggle_rewrite' => 'mr.reload',
                'ss/cat/' . $cat->id . '/less/reset'          => 'mr.reload',
                'ss/cat/' . $cat->id . '/less/save'           => 'mr.reload',
                'ss/cat/' . $cat->id . '/less/update-self'    => 'mr.reload',
            ],
            '.r' => [
                'reload'    => $this->_abs('>xhr:reload', [
                    'cat' => $catXPack
                ]),
                'updateCss' => $this->_p('>xhr:updateCss')
            ]
        ]);

        return $v;
    }
}
