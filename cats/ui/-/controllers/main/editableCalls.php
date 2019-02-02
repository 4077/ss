<?php namespace ss\cats\ui\controllers\main;

class EditableCalls extends \Controller
{
    public function run()
    {
        if ($cat = $this->unpackModel('cat')) {
            if ($this->a('ss:*')) {
                $this->app->html->addContainer('ss_cpanel', $this->c('cpanel~:view', ['cat' => $cat]));
            }

            if (ss()->globalEditable()) {
                pusher()->subscribe();

                $catPack = pack_model($cat);

                $pageDialogData = $this->c('\std\ui\dialogs~:getData:page|ss/cat');

                if (!empty($pageDialogData['follow_route'])) {
                    $this->c('\std\ui\dialogs~:update:page|ss/cats', [
                        'data'  => [
                            'cat' => $catPack
                        ],
                        'title' => $this->_abs('\ss\cats\cp\page~dialogTitle:view', [
                            'cat' => $catPack
                        ])
                    ]);
                }

                $this->c('\std\ui\dialogs~:update:pageNode|ss/cats', ['data' => ['cat' => $catPack]]);
                $this->c('\std\ui\dialogs~:update:pagesTree|ss/cats', ['data' => ['cat' => $catPack]]);

                $this->c('\std\ui\dialogs~:addContainer:ss/cats');
            }
        }
    }
}
