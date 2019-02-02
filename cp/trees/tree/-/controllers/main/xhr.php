<?php namespace ss\cp\trees\tree\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function selectTab()
    {
        if ($tree = $this->unxpackModel('tree')) {
            $this->s('~:tab|', $this->data('tab'), RR);

            $this->c('~:reload|', [
                'tree' => $tree
            ]);
        }
    }
}
