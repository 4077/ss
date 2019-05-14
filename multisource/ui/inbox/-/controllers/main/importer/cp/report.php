<?php namespace ss\multisource\ui\inbox\controllers\main\importer\cp;

class Report extends \Controller
{
    private $attachment;

    private $attachmentXPack;

    public function __create()
    {
        if ($this->attachment = $this->unpackModel('attachment')) {
            $this->attachmentXPack = xpack_model($this->attachment);

            $this->instance_($this->attachmentXPack);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $attachment = $this->attachment;

        $aiPivots = \ss\multisource\models\InboxAttachmentImporter::with('importer')
            ->where('attachment_id', $attachment->id)
            ->get();

        $aiPivotsByImportersIds = table_rows_by($aiPivots, 'importer_id');

        $importersIds = [];
        $matchedImportersIds = [];
        $notMatchedImportersIds = [];
        foreach ($aiPivots as $pivot) {
            $importersIds[] = $pivot->importer_id;

            if ($pivot->matched) {
                $matchedImportersIds[$pivot->importer_id] = true;
            } else {
                $notMatchedImportersIds[$pivot->importer_id] = true;
            }
        }

        $importers = table_rows_by_id(\ss\multisource\models\Importer::whereIn('id', $importersIds)->orderBy('position')->get());
        $importersIdsByPosition = array_keys($importers);

        $matchedImportersIdsByPosition = array_keys(map($matchedImportersIds, $importersIdsByPosition));
        $notMatchedImportersIdsByPosition = array_keys(map($notMatchedImportersIds, $importersIdsByPosition));

        $sequence = array_merge($matchedImportersIdsByPosition, $notMatchedImportersIdsByPosition);

        foreach ($sequence as $importerId) {
            $pivot = $aiPivotsByImportersIds[$importerId];
            $pivotXPack = xpack_model($pivot);

            $importer = $pivot->importer;

            $v->assign('importer', [
                'PIVOT_ID'      => $pivot->id,
                'ID'            => $importer->id,
                'NAME'          => $importer->name,
                'MATCHED_CLASS' => $pivot->matched ? 'matched' : '',
                'IMPORT_BUTTON' => $pivot->matched
                    ? $this->c('\std\ui button:view', [
                        'ctrl'  => [
                            'path' => '>xhr:import',
                            'data' => [
                                'ai_pivot' => $pivotXPack,
                                'sync'     => true
                            ]
                        ],
                        'path'  => '>xhr:import',
                        'data'  => [
                            'ai_pivot' => $pivotXPack,
                        ],
                        'class' => 'import_button ' . ($pivot->import_proc_pid ? 'hidden' : ''),
                        'icon'  => 'fa fa-angle-double-right'
                    ])
                    : '',
            ]);

            $matches = _j($pivot->matches) ?? [];

            foreach ($matches as $coord => $data) {
                $v->assign('importer/cell', [
                    'COORD'       => $coord,
                    'MATCH_CLASS' => $data['matched'] ? 'matched' : 'not_matched',
                    'TITLE'       => !$data['matched']
                        ? implode("\n", [
                            'Ожидаемое значение: ' . $this->formatValue($data['expected']),
                            'Значение в ячейке: ' . $this->formatValue($data['value']),
                            'Значение в ячейке (latin1): ' . $this->formatValue($data['converted'])
                        ])
                        : $this->formatValue($data['value'], $attachment->encoding == 'latin1')
                ]);
            }

            $sheetsNames = _j($attachment->sheets_names) ?? [];

            foreach ($sheetsNames as $sheetIndex => $sheetName) {
                $v->assign('importer/sheet', [
                    'NAME'           => $sheetName,
                    'DETECTED_CLASS' => $sheetIndex === $pivot->sheet_index ? 'detected' : ''
                ]);
            }

            if ($pivot->imported_at > 0) {
                $v->assign('importer/imported_datetime', [
                    'CONTENT' => \Carbon\Carbon::parse($pivot->imported_at)->format('d.m.Y H:i:s')
                ]);
            }

            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|') . " .importer[importer_id='" . $importer->id . "']",
                'path'     => '>xhr:openImporter',
                'data'     => [
                    'importer' => xpack_model($importer)
                ]
            ]);
        }

        $this->css();

        $runningAIPivots = \ss\multisource\models\InboxAttachmentImporter::where('import_proc_pid', '!=', false)->get();

        $importXPids = [];
        foreach ($runningAIPivots as $pivot) {
            if ($process = $this->app->processDispatcher->open($pivot->import_proc_pid)) {
                $importXPids[] = [
                    'aiPivotId' => $pivot->id,
                    'xpid'      => $process->getXPid()
                ];
            } else {
                $pivot->import_proc_pid = false;
                $pivot->save();
            }
        }

        $this->widget(':|', [
            'importXPids' => $importXPids
        ]);

        return $v;
    }

    private function formatValue($value, $decode = false)
    {
        if ($decode) {
            if (!$convertedValue = @iconv('cp1251', 'utf-8', iconv('', 'latin1', $value))) {
                $convertedValue = @mb_convert_encoding(mb_convert_encoding($value, 'latin1', ''), 'utf-8', 'cp1251');
            }

            $value = $convertedValue;
        }

        return str_replace([' ', "\n"], ['&#9679;', '&crarr;'], $value);
    }
}
