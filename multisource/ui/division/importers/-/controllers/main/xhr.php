<?php namespace ss\multisource\ui\division\importers\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function create()
    {
        if ($division = $this->unxpackModel('division')) {
            $division->importers()->create([]);

            $this->c('~:reload', [], 'division');
        }
    }

    public function open()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $this->c('\std\ui\dialogs~:open:importer, ss|ss/multisource/division', [
                'path'          => '^ui/division/importer~:view',
                'data'          => [
                    'importer' => pack_model($importer)
                ],
                'pluginOptions' => [
                    'title' => 'Настройки загрузчика ' . ($importer->name ?: '...')
                ],
                'default'       => [

                ]
            ]);
        }
    }

    public function duplicate()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $newImporterData = $importer->toArray();
            $newImporterData['enabled'] = false;

            $division = $importer->division;

            $newImporter = $division->importers()->create($newImporterData);

            $newImporter->position = $importer->position + 5;
            $newImporter->save();

            \DB::statement('SET @i := 0;');

            $division->importers()->orderBy('position')->update(['position' => \DB::raw('(@i := @i + 10)')]);

            $this->c('~:reload', [
                'division' => $division
            ]);
        }
    }
}
