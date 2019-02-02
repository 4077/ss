<?php namespace ss\cp\trees\tree\own\cats\controllers\main;

class NodeControl extends \Controller
{
    private $cat;

    private $treeInstance;

    private $viewInstance;

    public function __create()
    {
        $this->cat = $this->unpackModel('cat');

        $this->treeInstance = $this->_instance();
        $this->viewInstance = path($this->_instance(), $this->cat->id);
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $cat = $this->cat;

        $name = $cat->short_name ?: $cat->name;

        $mode = in_array($cat->id, $this->data['merge_ids'])
            ? 'merge'
            : (in_array($cat->id, $this->data['diff_ids']) ? 'diff' : '');

        $v->assign([
                       'ID'                       => $cat->id,
                       'NAME'                     => $name ? $name : '...',
                       'MODE_CLASS'               => $mode,
                       'CONTAINER_CLASS'          => $cat->type == 'container' ? 'container' : '',
                       'ENABLED_CLASS'            => in_array($cat->id, $this->data['enabled_ids']) ? 'enabled' : '',
                       'AUTO_ENABLED_CLASS'       => in_array($cat->id, $this->data['auto_enabled_ids']) ? 'auto_enabled' : '',
                       'HAS_NESTED_ENABLED_CLASS' => in_array($cat->id, $this->data['has_nested_enabled_ids']) ? 'has_nested_enabled' : '',
                       'MERGE_BUTTON'             => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggle:merge|',
                           'data'    => [
                               'cat' => xpack_model($cat)
                           ],
                           'class'   => 'merge button',
                           'content' => '<div class="icon"></div>'
                       ]),
                       'DIFF_BUTTON'              => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggle:diff|',
                           'data'    => [
                               'cat' => xpack_model($cat)
                           ],
                           'class'   => 'diff button',
                           'content' => '<div class="icon"></div>'
                       ])
                   ]);

        $this->css(':\js\jquery\ui icons');

        return $v;
    }
}
