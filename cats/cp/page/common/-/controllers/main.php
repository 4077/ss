<?php namespace ss\cats\cp\page\common\controllers;

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

    public function accessDeniedView()
    {
        return 'access denied';
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        if (ss()->cats->isEditable($this->cat)) {
            $v = $this->v('|' . $this->viewInstance);

            $cat = $this->cat;
            $catXPack = xpack_model($cat);

            $fields = $this->c('~app:getFields');

            foreach ($fields as $field) {
                $v->assign(strtoupper($field), $cat->{$field});
            }

            $toggleButtonsAccess = $this->a('ss:moderation');

            $v->assign([
                           'ENABLED_BUTTON'   => $this->c('\std\ui button:view', [
                               'visible' => $toggleButtonsAccess,
                               'path'    => '>xhr:toggleEnabled',
                               'data'    => [
                                   'cat' => $catXPack
                               ],
                               'class'   => 'button enabled ' . ($cat->enabled ? 'pressed' : ''),
                               'content' => $cat->enabled ? 'включена' : 'выключена'
                           ]),
                           'PUBLISHED_BUTTON' => $this->c('\std\ui button:view', [
                               'visible' => $toggleButtonsAccess,
                               'path'    => '>xhr:togglePublished',
                               'data'    => [
                                   'cat' => $catXPack
                               ],
                               'class'   => 'button published ' . ($cat->published ? 'pressed' : ''),
                               'content' => $cat->published ? 'опубликована' : 'не опубликована'
                           ]),
                           'URL'              => abs_url($cat->route_cache),
                           'ROUTE_CACHE'      => $cat->route_cache,
                           'IMAGES'           => $this->c('\std\images\ui~:view|ss/cats/page', [
                               'imageable' => pack_model($cat),
                               'dev_info'  => false,
                               'href'      => [
                                   'enabled' => true
                               ],
                               'callbacks' => [
                                   'update' => $this->_abs('>app:imagesUpdate', [
                                       'cat' => '%imageable'
                                   ])
                               ]
                           ])
                       ]);

            $this->css();

            $this->widget(':|' . $this->viewInstance, [
                '.payload' => [
                    'cat' => $catXPack
                ],
                '.r'       => [
                    'updateField' => $this->_p('>xhr:updateField|'),
                    'reload'      => $this->_abs('>xhr:reload|', [
                        'cat' => $catXPack
                    ])
                ],
                'catId'    => $cat->id
            ]);

            return $v;
        } else {
            return $this->c('\ss\cats\cp accessDenied:view');
        }
    }
}
