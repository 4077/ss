<?php namespace ss\suppliers\import\controllers\main;

class Importer extends \Controller
{
    public function import()
    {
        $importerData = $this->data('importer_data');

        return $this->c($importerData['importer_path'] . ':handle', $importerData);
    }
}
