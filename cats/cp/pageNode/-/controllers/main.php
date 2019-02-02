<?php namespace ss\cats\cp\pageNode\controllers;

class Main extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        if ($page = $this->unpackModel('cat')) {
            if (ss()->cats->isEditable($page)) {
                $v = $this->v('|');

                $pageXPack = xpack_model($page);

                $v->assign([
                               'PAGE'          => $this->c('>page:view', [
                                   'cat' => $page
                               ]),
                               'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                                   'path'    => '>xhr:create',
                                   'data'    => [
                                       'cat' => $pageXPack
                                   ],
                                   'class'   => 'create_button',
                                   'content' => 'Создать'
                               ])
                           ]);

                $containers = $page->containers()->orderBy('position')->get();

                foreach ($containers as $container) {
                    $v->assign('container', [
                        'ID'      => $container->id,
                        'CONTENT' => $this->c('>container:view', [
                            'cat'  => $container,
                            'page' => $page
                        ])
                    ]);
                }

                $this->c('\std\ui sortable:bind', [
                    'selector'       => $this->_selector('|') . ' .containers',
                    'items_id_attr'  => 'cat_id',
                    'path'           => '>xhr:arrange',
                    'data'           => [
                        'cat' => $pageXPack
                    ],
                    'plugin_options' => [
                        'distance' => 20,
                        'axis'     => 'y',
                        'cancel'   => '.scroller'
                    ]
                ]);

                $this->css(':\css\std~');

                $this->widget(':', [
                    '.r'                => [
                        'reload' => $this->_abs('>xhr:reload', [
                            'cat' => xpack_model($page)
                        ])
                    ],
                    'catId'             => $page->id,
                    'containerSelector' => $this->_selector('>container:')
                ]);

                return $v;
            } else {
                return $this->c('\ss\cats\cp accessDenied:view');
            }
        }
    }
}
