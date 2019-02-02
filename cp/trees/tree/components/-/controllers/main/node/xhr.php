<?php namespace ss\cp\trees\tree\components\controllers\main\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->s('~:selected_cat_id|', $cat->id, RR);

            $this->se('ss/trees/tree/components/cat_select')->trigger();
        }
    }

    public function toggle($mode)
    {
        $tree = $this->unxpackModel('tree');
        $cat = $this->unxpackModel('cat');
        $catType = $this->data('cat_type');
        $type = $this->data('type');

        if ($tree && $cat && $catType && in($catType, 'page, container') && in($mode, 'merge, diff')) {
            ss()->trees->toggleComponentsCatPivot($tree, $cat, $catType, $type, $mode);

            $this->c('<<:reload|', [
                'tree' => $tree
            ]);
        }
    }

    public function updateAccess()
    {
        $cat = $this->unxpackModel('cat');
        $tree = $this->unxpackModel('tree');
        $catType = $this->data('cat_type');
        $type = $this->data('type');

        if ($cat && $tree && $catType && in($catType, 'page, container')) {
            $txt = \std\ui\Txt::value($this);

            ss()->trees->setComponentsCatPivotAccess($tree, $cat, $catType, $type, $txt->value);

            $txt->response();
        }
    }
}
