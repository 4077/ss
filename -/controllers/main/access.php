<?php namespace ss\controllers\main;

class Access extends \Controller
{
    public function isEditable()
    {
        $target = $this->data('target');

        if ($target instanceof \ss\models\Cat) {
            return ss()->cats->isEditable($target);
        }

        if ($target instanceof \ss\models\Product) {
            return ss()->products->isEditable($target);
        }
    }
}
