<?php namespace ss\flow\ui\channels\controls\treeSelector\controllers\main\tree\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($tree = $this->unxpackModel('node')) {

            // todo ss()->flow->createNode($tree)
            if (!\ss\flow\models\Node::where('tree_id', $tree->id)->first()) {
                \ss\flow\models\Node::create([
                                                 'tree_id' => $tree->id
                                             ]);
            }

            $this->c('\std\ui\dialogs~:close:treeSelector|ss/flow/channels');
        }
    }
}
