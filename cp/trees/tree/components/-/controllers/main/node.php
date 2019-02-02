<?php namespace ss\cp\trees\tree\components\controllers\main;

class Node extends \Controller
{
    private $cat;

    private $tree;

    private $viewInstance;

    public function __create()
    {
        $this->cat = $this->data['cat'];
        $this->tree = $this->unpackModel('tree');

        $this->viewInstance = $this->cat->id;
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $isRootCat = $this->data['root_cat_id'] == $this->cat->id;

        $cat = $this->cat;
        $tree = $this->tree;

        $catXPack = xpack_model($cat);
        $treeXPack = xpack_model($tree);

        $catType = $this->data('cat_type');
        $type = $this->data('type');

        $mode = in_array($cat->id, $this->data['merge_ids'])
            ? 'merge'
            : (in_array($cat->id, $this->data['diff_ids']) ? 'diff' : '');

        $v->assign([
                       'ID'                       => $cat->id,
                       'ROOT_CLASS'               => $isRootCat ? 'root' : '',
                       'NAME'                     => $cat->name ?: '...',
                       'MODE_CLASS'               => $mode,
                       'ENABLED_CLASS'            => in_array($cat->id, $this->data['enabled_ids']) ? 'enabled' : '',
                       'AUTO_ENABLED_CLASS'       => in_array($cat->id, $this->data['auto_enabled_ids']) ? 'auto_enabled' : '',
                       'HAS_NESTED_ENABLED_CLASS' => in_array($cat->id, $this->data['has_nested_enabled_ids']) ? 'has_nested_enabled' : '',
                       'ACCESS'                   => $this->c('\std\ui txt:view', [
                           'path'              => '>xhr:updateAccess|',
                           'data'              => [
                               'cat'      => $catXPack,
                               'tree'     => $treeXPack,
                               'cat_type' => $catType,
                               'type'     => $type
                           ],
                           'class'             => 'txt',
                           'fitInputToClosest' => '.content',
                           'placeholder'       => '*',
                           'content'           => $this->data('access_by_cat_id/' . $cat->id)
                       ]),
                       'MERGE_BUTTON'             => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggle:merge|',
                           'data'    => [
                               'cat'      => $catXPack,
                               'tree'     => $treeXPack,
                               'cat_type' => $catType,
                               'type'     => $type
                           ],
                           'class'   => 'merge button',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DIFF_BUTTON'              => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggle:diff|',
                           'data'    => [
                               'cat'      => $catXPack,
                               'tree'     => $treeXPack,
                               'cat_type' => $catType,
                               'type'     => $type
                           ],
                           'class'   => 'diff button',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        $this->css(':\js\jquery\ui icons');

        if (!$isRootCat) {
            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|' . $this->viewInstance),
                'path'     => '>xhr:select|',
                'data'     => [
                    'cat' => $catXPack
                ]
            ]);
        }

        return $v;
    }
}
