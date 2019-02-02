<?php namespace ss\cp\trees\connections\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($tree = $this->unxpackModel('tree')) {
            $this->s('~:selected_tree_id|', $tree->id, RR);

            $this->c('~:reload|');
        }
    }

    public function selectConnection()
    {
        if ($tree = $this->unxpackModel('tree')) {
            if ($selectedTree = \ss\models\Tree::find($this->s('~:selected_tree_id|'))) {

                if (\ss\models\TreesConnection::where('source_id', $selectedTree->id)->where('target_id', $tree->id)->first()) {
                    $selectedConnection = [
                        'source_tree_id' => $selectedTree->id,
                        'target_tree_id' => $tree->id
                    ];
                } elseif (\ss\models\TreesConnection::where('source_id', $tree->id)->where('target_id', $selectedTree->id)->first()) {
                    $selectedConnection = [
                        'source_tree_id' => $tree->id,
                        'target_tree_id' => $selectedTree->id
                    ];
                }

                if (isset($selectedConnection)) {
                    $this->s('~:selected_connection|', $selectedConnection, RR);

                    $this->c('~:reload|');
                }
            }
        }
    }

    public function selectTab() // todo del
    {
        $this->s('~:tab|', $this->data('tab'), RR);

        $this->c('~:reload|');
    }

    public function selectAdapter()
    {
        $this->s('~:selected_adapter_name|', $this->data('name'), RR);

        $this->c('~:reload|');
    }
}
