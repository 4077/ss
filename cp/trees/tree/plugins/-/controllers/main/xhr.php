<?php namespace ss\cp\trees\tree\plugins\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($tree = $this->unxpackModel('tree')) {
            $this->s('~:selected_plugin_name', $this->data('name'), RR);

            $this->c('~:reload', [
                'tree' => $tree
            ]);
        }
    }

    public function toggle()
    {
        if ($tree = $this->unxpackModel('tree')) {
            $pluginData = ss()->trees->plugins->pluginData($tree, $this->data('name'));

            invert($pluginData['enabled']);

            ss()->trees->plugins->pluginData($tree, $this->data('name'), false, $pluginData);

            $this->c('~:reload', [
                'tree' => $tree
            ]);
        }
    }
}
