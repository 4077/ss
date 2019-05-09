<?php namespace ss\multisource\ui\division\importersControls\warehouseSelector\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload', [], 'importer');
    }

    public function select()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $importer->warehouse_id = $this->data('value');
            $importer->save();

            pusher()->trigger('ss/multisource/importers/warehouseSelect', [
                'importerId'    => $importer->id,
                'importerXPack' => xpack_model($importer)
            ]);
        }
    }
}
