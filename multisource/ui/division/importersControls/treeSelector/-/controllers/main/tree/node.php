<?php namespace ss\multisource\ui\division\importersControls\treeSelector\controllers\main\tree;

class Node extends \Controller
{
    public function view()
    {
        $node = $this->unpackModel('node');
        $nodeXPack = xpack_model($node);

        $v = $this->v('|' . $node->id);

        $v->assign([
                       'ID'              => $node->id,
                       'MODE_ICON_CLASS' => 'fa fa-' . ($node->mode == 'folders' ? 'folder' : 'file'),
                       'NAME'            => $node->name ?: '...'
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|' . $node->id),
            'path'     => '>xhr:select|',
            'data'     => [
                'node'     => $nodeXPack,
                'importer' => $this->data('importer')
            ]
        ]);

        $this->css();

        return $v;
    }
}
