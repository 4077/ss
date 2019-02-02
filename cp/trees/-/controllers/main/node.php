<?php namespace ss\cp\trees\controllers\main;

class Node extends \Controller
{
    private $node;

    public function __create()
    {
        if ($this->node = $this->unpackModel('node')) {

        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->node->id)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->node->id);

        $node = $this->node;
        $nodeXPack = xpack_model($node);

        $isRootNode = $this->data['root_node_id'] == $node->id;

        $v->assign([
                       'ID'                    => $node->id,
                       'ROOT_CLASS'            => $isRootNode ? 'root' : '',
                       'MODE_ICON_CLASS'       => !$isRootNode ? 'fa fa-' . ($node->mode == 'folders' ? 'folder' : 'file') : 'hidden',
                       'NAME'                  => $isRootNode
                           ? ''
                           : $this->c('\std\ui txt:view', [
                               'path'                => '>xhr:rename',
                               'data'                => [
                                   'node' => $nodeXPack
                               ],
                               'class'               => 'txt',
                               'fitInputToClosest'   => '.content',
                               'placeholder'         => '...',
                               'editTriggerSelector' => $this->_selector('|' . $node->id) . " .rename.button",
                               'content'             => $node->name
                           ]),
                       'RENAME_BUTTON'         => $isRootNode
                           ? ''
                           : $this->c('\std\ui tag:view', [
                               'attrs'   => [
                                   'class' => 'rename button',
                                   'hover' => 'hover',
                                   'title' => 'Переименовать'
                               ],
                               'content' => '
                                    <div class="icon">
                                        <div class="fa fa-pencil"></div>                            
                                    </div>
                               '
                           ]),
                       'CREATE_FOLDERS_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createFolders|',
                           'data'    => [
                               'node' => $nodeXPack
                           ],
                           'class'   => 'create folders button',
                           'title'   => 'Создать дерево папок',
                           'content' => '
                                <div class="icon">
                                    <div class="main fa fa-folder"></div>
                                    <div class="plus fa fa-plus"></div>
                                </div>
                           '
                       ]),
                       'CREATE_PAGES_BUTTON'   => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:createPages|',
                           'data'    => [
                               'node' => $nodeXPack
                           ],
                           'class'   => 'create pages button',
                           'title'   => 'Создать дерево страниц',
                           'content' => '
                                <div class="icon">
                                    <div class="main fa fa-file"></div>
                                    <div class="plus fa fa-plus"></div>
                                </div>
                           '
                       ]),
                       'CREATE_BUTTON'         => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:create|',
                           'data'    => [
                               'node' => $nodeXPack
                           ],
                           'class'   => 'create button',
                           'title'   => 'Создать',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DUPLICATE_BUTTON'      => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:duplicate|',
                           'data'    => [
                               'node' => $nodeXPack
                           ],
                           'class'   => 'button duplicate',
                           'title'   => 'Дублировать',
                           'icon'    => 'fa fa-clone'
                       ]),
                       'DELETE_BUTTON'         => $this->c('\std\ui button:view', [
                           'visible' => !$isRootNode,
                           'path'    => '>xhr:delete|',
                           'data'    => [
                               'node' => $nodeXPack
                           ],
                           'class'   => 'button delete',
                           'title'   => 'Удалить',
                           'icon'    => 'fa fa-trash-o'
                       ])
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $node->id),
            'path'     => '>xhr:select|',
            'data'     => [
                'node' => $nodeXPack
            ]
        ]);

        $this->css(':\js\jquery\ui icons');

        return $v;
    }
}
