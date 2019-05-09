<?php namespace ss\flow\ui\channels\controllers\main\contextmenu;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function openTreeSelector()
    {
        $this->c('\std\ui\dialogs~:open:treeSelector, ss|ss/flow/channels', [
            'path'          => 'controls/treeSelector~:view',
            'pluginOptions' => [
                'title' => ''
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
