<?php namespace ss\flow\ui\channels\controls\treeSelector\controllers\main\tree;

class Node extends \Controller
{
    public function view()
    {
        $node = $this->unpackModel('node');
        $nodeXPack = xpack_model($node);

        $v = $this->v('|' . $node->id);

        $added = in_array($node->id, $this->data['added_trees_ids']);

        $v->assign([
                       'ID'              => $node->id,
                       'ADDED_CLASS'     => $added ? 'added' : '',
                       'MODE_ICON_CLASS' => 'fa fa-' . ($node->mode == 'folders' ? 'folder' : 'file'),
                       'NAME'            => $node->name ?: '...'
                   ]);

        if (!$added) {
            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|' . $node->id),
                'path'     => '>xhr:select|',
                'data'     => [
                    'node' => $nodeXPack
                ]
            ]);
        }

        $this->css();

        return $v;
    }
}
