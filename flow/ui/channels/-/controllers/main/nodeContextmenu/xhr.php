<?php namespace ss\flow\ui\channels\controllers\main\nodeContextmenu;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function delete()
    {
        if ($node = \ss\flow\models\Node::find($this->data('node_id'))) {
            \ss\flow\models\Channel::where('source_id', $node->id)->delete();
            \ss\flow\models\Channel::where('target_id', $node->id)->delete();

            $node->delete();
        }
    }
}
