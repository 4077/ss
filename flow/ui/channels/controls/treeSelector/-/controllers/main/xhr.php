<?php namespace ss\flow\ui\channels\controls\treeSelector\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload', [], 'importer');
    }

    public function openTreesDialog()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $this->c('\std\ui\dialogs~:open:importerTreeSelector, ss|ss/multisource/division', [
                'path'          => '~tree:view',
                'data'          => [
                    'importer' => pack_model($importer)
                ],
                'pluginOptions' => [
                    'title' => 'Ветка для загрузчика ' . ($importer->name ?: '...')
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 350,
                        'height' => 400
                    ]
                ]
            ]);
        }
    }
}
