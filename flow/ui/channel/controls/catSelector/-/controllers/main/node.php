<?php namespace ss\flow\ui\channel\controls\catSelector\controllers\main;

class Node extends \Controller
{
    private $cat;

    private $viewInstance;

    public function __create()
    {
        if ($cat = $this->unpackModel('cat')) {
            $this->cat = $cat;

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

        $cat = $this->cat;
        $catXPack = xpack_model($cat);

        $isRootNode = $this->data['root_node_id'] == $this->cat->id;

        $v->assign([
                       'ID'              => $cat->id,
                       'ROOT_CLASS'      => $isRootNode ? 'root' : '',
                       'CONTAINER_CLASS' => $cat->type == 'container' ? 'container' : '',
                       'DISABLED_CLASS'  => $this->cat->enabled ? '' : 'disabled',
                       'NAME'            => ss()->cats->getShortName($cat) ?: '...',
                       'SELECTED_CLASS'  => in($cat->id, $this->data('selected_cats_ids')) ? 'selected' : ''
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $this->viewInstance),
            'path'     => '>xhr:toggle|',
            'data'     => [
                'cat'     => $catXPack,
                'channel' => $this->xpackModel('channel'),
                'type'    => $this->data('type')
            ]
        ]);

        $this->css(':\js\jquery\ui icons');

        return $v;
    }
}
