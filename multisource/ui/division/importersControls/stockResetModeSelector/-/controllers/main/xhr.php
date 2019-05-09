<?php namespace ss\multisource\ui\division\importersControls\stockResetModeSelector\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $importer->stock_reset_mode = $this->data('value');
            $importer->save();
        }
    }
}
