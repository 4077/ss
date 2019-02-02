<?php namespace ss\cats\cp\container\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function selectTab()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->s('~:tab', $this->data('tab'), RR);

            $dialogData = $this->c('\std\ui\dialogs~:getData:container|');

            if ($tabDialogData = $this->c('app:getTabDialogData')) {
                ra($dialogData, $tabDialogData);
            }

            $this->c('\std\ui\dialogs~:updateAndReload:container|', $dialogData);
        }
    }
}
