<?php namespace ss\cats\cp\pagesTree\controllers\main;

class Node extends \Controller
{
    private $cat;

    private $viewInstance;

    public function __create()
    {
        if ($cat = $this->unpackModel('cat')) {
            $this->cat = $cat;

            $this->dmap('|', 'root_node_id, enabled_ids'); // todo enabled_ids

            $this->viewInstance = $cat->id;
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
        $v = $this->v('|' . $this->viewInstance);

        $ss = ss();

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $isRootNode = $this->data['root_node_id'] == $this->cat->id;

        $name = $ss->cats->getName($cat) or
        $name = '...';

        $editable = $ss->cats->isEditable($cat);

        $class = [];

        $cat->route_cache && $cat->route_cache == $this->data('route') and $class[] = 'selected';
        $isRootNode and $class[] = 'root';
        !$this->cat->enabled and $class[] = 'disabled';
        !$this->cat->published and $class[] = 'not_published';
        !$editable and $class[] = 'locked';

        $v->assign([
                       'ID'                      => $cat->id,
                       'CLASS'                   => implode(' ', $class),
                       'NAME'                    => $name,
                       'PAGE_DIALOG_BUTTON'      => $this->c('\std\ui button:view', [
                           'visible' => $editable,
                           'path'    => '>xhr:pageDialog',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button',
                           'title'   => 'Настройки',
                           'icon'    => 'fa fa-cog'
                       ]),
                       'PAGE_NODE_DIALOG_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => $editable,
                           'path'    => '>xhr:pageNodeDialog',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button',
                           'title'   => 'Контейнеры',
                           'icon'    => 'fa fa-cube'
                       ]),
                       //                       'CREATE_BUTTON'           => $this->c('\std\ui button:view', [
                       //                           'path'  => '>xhr:create|',
                       //                           'data'  => [
                       //                               'cat' => $catXPack
                       //                           ],
                       //                           'class' => 'button',
                       //                           'title' => 'Создать',
                       //                           'icon'  => 'fa fa-plus'
                       //                       ]),
                       //                       'DELETE_BUTTON'           => $this->c('\std\ui button:view', [
                       //                           'visible' => !$isRootNode,
                       //                           'path'    => '>xhr:delete|',
                       //                           'data'    => [
                       //                               'cat' => $catXPack
                       //                           ],
                       //                           'class'   => 'button',
                       //                           'title'   => 'Удалить',
                       //                           'icon'    => 'fa fa-trash-o'
                       //                       ])
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $this->viewInstance),
            'path'     => '>xhr:select|',
            'data'     => [
                'cat' => $catXPack
            ]
        ]);

        $this->css(':\js\jquery\ui icons');

        return $v;
    }
}
