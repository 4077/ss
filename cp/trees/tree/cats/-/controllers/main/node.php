<?php namespace ss\cp\trees\tree\cats\controllers\main;

class Node extends \Controller
{
    private $tree;

    private $cat;

    private $viewInstance;

    public function __create()
    {
        if ($cat = $this->unpackModel('cat')) {
            $this->cat = $cat;
            $this->tree = $this->unpackModel('tree') ?: $cat->tree;

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

        $tree = $this->tree;

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $isRootNode = $this->data['root_node_id'] == $this->cat->id;

        $v->assign([
                       'ID'                      => $cat->id,
                       'ROOT_CLASS'              => $isRootNode ? 'root' : '',
                       'CONTAINER_CLASS'         => $cat->type == 'container' ? 'container' : '',
                       'NAME'                    => $isRootNode
                           ? ''
                           : $this->c('\std\ui txt:view', [
                               'path'                => '>xhr:rename',
                               'data'                => [
                                   'node' => $catXPack
                               ],
                               'class'               => 'txt',
                               'fitInputToClosest'   => '.content',
                               'placeholder'         => '...',
                               'editTriggerSelector' => $this->_selector('|' . $cat->id) . " .rename.button",
                               'content'             => ss()->cats->getShortName($cat) ?: '...'
                           ]),
                       'DISABLED_CLASS'          => $this->cat->enabled ? '' : 'disabled',
                       'CREATE_FOLDER_BUTTON'    => $this->c('\std\ui button:view', [
                           'visible' => $tree->mode == 'folders',
                           'path'    => '>xhr:createFolder|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'create folder button',
                           'title'   => 'Создать папку',
                           'content' => '
                                <div class="icon">
                                    <div class="main fa fa-folder"></div>
                                    <div class="plus fa fa-plus"></div>
                                </div>
                           '
                       ]),
                       'CREATE_PAGE_BUTTON'      => $this->c('\std\ui button:view', [
                           'visible' => $tree->mode == 'pages' && $cat->type == 'page',
                           'path'    => '>xhr:createPage|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'create page button',
                           'title'   => 'Создать страницу',
                           'content' => '
                                <div class="icon">
                                    <div class="main fa fa-file"></div>
                                    <div class="plus fa fa-plus"></div>
                                </div>
                           '
                       ]),
                       'CREATE_CONTAINER_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => $tree->mode == 'pages' && $cat->type == 'page',
                           'path'    => '>xhr:createContainer|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'create container button',
                           'title'   => 'Создать контейнер',
                           'content' => '
                                <div class="icon">
                                    <div class="main fa fa-cube"></div>
                                    <div class="plus fa fa-plus"></div>
                                </div>
                           '
                       ]),
                       'DUPLICATE_BUTTON'        => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:duplicate|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button duplicate',
                           'title'   => 'Дублировать',
                           'icon'    => 'fa fa-clone'
                       ]),
                       'DELETE_BUTTON'           => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:delete|',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button delete',
                           'title'   => 'Удалить',
                           'icon'    => 'fa fa-trash-o'
                       ])
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
