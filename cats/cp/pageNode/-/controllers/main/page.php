<?php namespace ss\cats\cp\pageNode\controllers\main;

class Page extends \Controller
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

        $v->assign([
                       'ID'            => $cat->id,
                       'NAME'          => ss()->cats->getName($cat),
                       'RELOAD_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:reloadPageUi',
                           'data'  => [
                               'cat' => $catXPack
                           ],
                           'class' => 'reload_button',
                           'icon'  => 'fa fa-refresh',
                           'title' => 'Обновить'
                       ])
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|'),
            'path'     => '>xhr:pageDialog',
            'data'     => [
                'cat' => $catXPack
            ]
        ]);

        $this->css();

        return $v;
    }
}
