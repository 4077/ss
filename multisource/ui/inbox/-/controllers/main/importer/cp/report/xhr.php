<?php namespace ss\multisource\ui\inbox\controllers\main\importer\cp\report;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function openImporter()
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

    public function import()
    {
        if ($aiPivot = $this->unxpackModel('ai_pivot')) {
            if ($this->data('sync')) {
                $this->c('^app/inbox~proc/importer:import', [
                    'ai_pivot' => pack_model($aiPivot)
                ]);
            } else {
                $this->proc('^app/inbox~proc/importer:import', [
                    'ai_pivot' => pack_model($aiPivot)
                ])->run();
            }
        }
    }
}
