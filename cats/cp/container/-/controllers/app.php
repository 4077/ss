<?php namespace ss\cats\cp\container\controllers;

class App extends \Controller
{
    public function updateTabDialogData()
    {
        $s = &$this->s('~');

        $tab = $s['tab'];

        remap($s['dialog_data'][$tab], $this->data, 'pluginOptions/width, pluginOptions/height');
    }

    public function getTabDialogData()
    {
        $s = &$this->s('~');

        return $s['dialog_data'][$s['tab'] ?? false] ?? [];
    }
}
