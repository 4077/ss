<?php namespace ss\cats\cp\pagesTree\controllers\main\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->app->response->href(abs_url($cat->route_cache));
        }
    }

    public function pageNodeDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\std\ui\dialogs~:open:pageNode, ss|ss/cats', [
                'path'    => '\ss\cats\cp\pageNode~:view|',
                'data'    => [
                    'cat' => pack_model($cat)
                ],
                'default' => [
                    'pluginOptions' => [
                        'width' => 300
                    ]
                ]
            ]);
        }
    }

    public function pageDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\ss\cats\cp dialogs:page|ss/cats', [
                'cat' => $cat
            ]);
        }
    }
}
