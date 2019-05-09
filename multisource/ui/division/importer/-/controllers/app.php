<?php namespace ss\multisource\ui\division\importer\controllers;

class App extends \Controller
{
    public function info()
    {
        if ($importer = \ss\multisource\models\Importer::find($this->data('importer_id'))) {
            return [
                'detect_map' => _j($importer->detect_map),
                'import_map' => _j($importer->import_map)
            ];
        }
    }
}
