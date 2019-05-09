<?php namespace ss\multisource\ui\division\importersControls\productsDeleteModeSelector\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $importer->products_delete_mode = $this->data('value');
            $importer->save();
        }
    }
}
