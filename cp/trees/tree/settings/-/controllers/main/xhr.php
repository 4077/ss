<?php namespace ss\cp\trees\tree\settings\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload|', [], 'tree');
    }

    public function toggleEditable()
    {
        if ($tree = $this->unxpackModel('tree')) {
            $tree->editable = !$tree->editable;
            $tree->save();

            pusher()->trigger('ss/tree/' . $tree->id . '/toggle_editable');
        }
    }
}
