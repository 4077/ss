<?php namespace ss\cp\trees\tree\settings\controllers;

class Main extends \Controller
{
    private $tree;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $tree = $this->tree;
        $treePack = pack_model($tree);
        $treeXPack = xpack_model($tree);

        $v->assign([
                       'TOGGLE_EDITABLE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEditable',
                           'data'    => [
                               'tree' => $treeXPack
                           ],
                           'class'   => 'toggle_editable_button ' . ($tree->editable ? 'pressed' : ''),
                           'content' => $tree->editable ? 'Редактирование включено' : 'Редактирование выключено'
                       ])
                       //                       'CONTENT' => $this->c('\std\ui\data~:view|' . $this->_nodeId() . '/' . $tree->id, [
                       //                           'read_call'  => ['>app:readData', ['tree' => $treePack]],
                       //                           'write_call' => ['>app:writeData', ['tree' => $treePack]],
                       //                       ])
                   ]);

        $this->css(':\css\std~');

        $this->widget(':|', [
            '.e' => [
                'ss/tree/' . $tree->id . '/toggle_editable' => 'mr.reload'
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload|', [
                    'tree' => $treeXPack
                ])
            ]
        ]);

        return $v;
    }
}
