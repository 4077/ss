<?php namespace ss\multisource\ui\division\importersControls\productNameLevelsSelector\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $importer->product_name_levels = $this->data('value');
            $importer->save();
        }
    }
}
