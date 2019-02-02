<?php namespace ss\cats\ui\cpanel\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function toggleGlobalEditable()
    {
        ss()->globalEditable(!ss()->globalEditable());

        $this->app->response->reload();
    }

    public function pagesTreeDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\std\ui\dialogs~:open:pagesTree, ss|ss/cats', [
                'path'    => '\ss\cats\cp\pagesTree~:view',
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

    public function pageNodeDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\std\ui\dialogs~:open:pageNode, ss|ss/cats', [
                'path'    => '\ss\cats\cp\pageNode~:view',
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
                'cat' => pack_model($cat),
                'ra'  => [
                    'follow_route' => true
                ]
            ]);
        }
    }

    public function setButtonsVisible()
    {
        $this->s('~:buttons_visible|', $this->data('value'), RR);

        pusher()->triggerSelfOthers('ss/cpanel/buttons_toggle', [
            'visible' => $this->data('value')
        ]);
    }
}
