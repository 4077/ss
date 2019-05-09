<?php namespace ss\multisource\ui\division\importersControls\articulZerofill\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $importer->articul_zerofill = $this->data('value');
            $importer->save();

//            pusher()->trigger('ss/multisource/importers/warehouseSelect', [
//                'importerId'    => $importer->id,
//                'importerXPack' => xpack_model($importer)
//            ]);
        }
    }
}
