<?php namespace ss\cp\trees\tree\components\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function setCatType()
    {
        if ($tree = $this->unxpackModel('tree')) {
            $catType = $this->data('cat_type');

            if (in($catType, 'page, container')) {
                $this->s('~:cat_type|', $catType, RR);

                $this->c('~:reload|', [
                    'tree' => $tree
                ]);
            }
        }
    }
}
