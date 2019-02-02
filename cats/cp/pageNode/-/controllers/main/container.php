<?php namespace ss\cats\cp\pageNode\controllers\main;

class Container extends \Controller
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

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $class = [];

        if (!$cat->enabled) {
            $class[] = 'disabled';
        }

        if (!$cat->published) {
            $class[] = 'not_published';
        }

        if (!$cat->output_enabled) {
            $class[] = 'output_disabled';
        }

        $v->assign([
                       'ID'            => $cat->id,
                       'CLASS'         => implode(' ', $class),
                       'NAME'          => ss()->cats->getShortName($cat),
                       'RELOAD_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:reloadContainerUi',
                           'data'  => [
                               'cat' => $catXPack
                           ],
                           'class' => 'reload_button',
                           'icon'  => 'fa fa-refresh',
                           'title' => 'Обновить'
                       ]),
                       'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:delete',
                           'data'  => [
                               'cat' => $catXPack
                           ],
                           'class' => 'delete button',
                           'icon'  => 'fa fa-trash-o'
                       ])
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|'),
            'path'     => '>xhr:containerDialog',
            'data'     => [
                'cat' => $catXPack
            ]
        ]);

        $this->css();

        return $v;
    }
}
