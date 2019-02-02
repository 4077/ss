<?php namespace ss\products\cp\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unxpackModel('cat')) {
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
                       'GRID'              => $this->c('>grid:view', [
                           'cat' => $cat
                       ]),
                       'CREATE_BUTTON'     => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'create_button green',
                           'content' => 'Создать товар'
                       ]),
                       'DELETE_ALL_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:deleteAll',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'delete_all_button',
                           'content' => 'Удалить все товары в этой категории'
                       ])
                   ]);

        $this->css(':\css\std~');

        $this->c('\std\ui\dialogs~:addContainer:ss/products');

        return $v;
    }
}
