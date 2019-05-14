<?php namespace ss\multisource\app\inbox\controllers\main\proc;

class ImportersDetector extends \Controller
{
    public function run()
    {
        $process = process();

        $attachments = \ss\multisource\models\InboxAttachment::where('detection_was_performed', false)->orderBy('id')->get();

        $count = count($attachments);
        $n = 0;

        foreach ($attachments as $attachment) {
            if ($process->handleIteration()) {
                break;
            }

            $n++;

            $this->proc(':detectAttachment', [
                'attachment' => pack_model($attachment)
            ])->run()->wait();

            $process->progress($n, $count);
        }
    }

    public function detectAttachment()
    {
        $process = process();

        $attachment = $this->unpackModel('attachment');

        if ($spreadsheet = $this->getSpreadsheet($attachment)) {
            $email = $attachment->message->from;

            \ss\multisource\models\InboxAttachmentImporter::where('attachment_id', $attachment->id)->delete();

            $this->log('attachment_id=' . $attachment->id . ' (' . $attachment->name . ' from ' . $email . ')');

            $detected = false;

            if ($importers = \ss\multisource\svc()->getImportersByEmail($email)) {
                foreach ($importers as $importer) {
                    $this->log();
                    $this->log('TRY importer: ' . ($importer->name ?: '...') . ' (division: ' . $importer->division->name . ')');

                    $attempt = $this->tryImporter($spreadsheet, $importer);

                    $aiPivot = $this->getAIPivot($attachment, $importer);

                    $aiPivot->matches = j_($attempt['matches']);
                    $aiPivot->sheet_index = $attempt['sheet_index'];

                    if ($attempt['matched']) {
                        $attachment->encoding = $attempt['encoding'];
                        $attachment->sheets_names = j_($attempt['sheets_names']);
                        $aiPivot->matched = true;

                        $this->log('DETECTED with encoding: ' . $attempt['encoding']);

                        $detected = true;
                    } else {
                        $aiPivot->matched = false;

                        $this->log('NOT DETECTED');
                    }

                    $aiPivot->save();
                }
            } else {
                $this->log('not found importers');
            }

            pusher()->trigger('ss/multisource/inbox/importerDetected', [
                'attachmentXPack' => xpack_model($attachment),
                'detected'        => $detected
            ]);

            $attachment->detection_was_performed = true;
            $attachment->save();

            unset($spreadsheet);
        }
    }

    private function tryImporter(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet, $importer)
    {
        if ($detectMap = _j($importer->detect_map) ?? []) {
            $matches = [];

            $fail = false;
            $encoding = false;

            $sheetsNames = $spreadsheet->getSheetNames();
            $convertedSheetsNames = [];

            if ($importerSheetName = $importer->sheet_name) {
                foreach ($sheetsNames as $sheetName) {
                    if (!$convertedSheetName = @iconv('cp1251', 'utf-8', iconv('', 'latin1', $sheetName))) {
                        $convertedSheetName = @mb_convert_encoding(mb_convert_encoding($sheetName, 'latin1', ''), 'utf-8', 'cp1251');
                    }

                    $convertedSheetsNames[] = $convertedSheetName;

                    if (mb_strtolower(trim($sheetName)) == mb_strtolower(trim($importerSheetName))) {
                        $worksheet = $spreadsheet->getSheetByName($sheetName);
                    }

                    if (mb_strtolower(trim($convertedSheetName)) == mb_strtolower(trim($importerSheetName))) {
                        $worksheet = $spreadsheet->getSheetByName($convertedSheetName);
                    }
                }

                if (!isset($worksheet)) {
                    $this->log('NOT FOUND WORKSHEET with name ' . $importerSheetName);
                }
            } else {
                $worksheet = $spreadsheet->getActiveSheet();
            }

            if (isset($worksheet)) {
                $sheetName = $worksheet->getTitle();
                $sheetIndex = array_search($sheetName, $sheetsNames);

                foreach ($detectMap as $coord => $expectedValue) {
                    $expectedValue = str_replace('\n', "\n", $expectedValue);

                    $value = (string)$worksheet->getCell($coord);

                    if (empty($value)) {
                        $convertedValue = $value;
                    } else {
                        if (!$convertedValue = @iconv('cp1251', 'utf-8', iconv('', 'latin1', $value))) {
                            $convertedValue = @mb_convert_encoding(mb_convert_encoding($value, 'latin1', ''), 'utf-8', 'cp1251');
                        }
                    }

                    $setEncoding = true;

                    if ($expectedValue == '~e') {
                        $matchExpected = $matchConverted = empty($value);

                        $expectedValueLogLabel = '[EMPTY]';
                        $expectedValueReportLabel = '[пустое]';

                        $setEncoding = false;
                    } elseif ($expectedValue == '~ne') {
                        $matchExpected = $matchConverted = !empty($value);

                        $expectedValueLogLabel = '[NOT EMPTY]';
                        $expectedValueReportLabel = '[не пустое]';

                        $setEncoding = false;
                    } else {
                        $matchExpected = $value == $expectedValue;
                        $matchConverted = $convertedValue == $expectedValue;

                        $expectedValueLogLabel = $expectedValue;
                        $expectedValueReportLabel = $expectedValue;
                    }

                    $this->log('test cell: ' . $coord);
                    $this->log('  > value: ' . $value);
                    $this->log('  > converted: ' . $convertedValue);
                    $this->log('  > expected: ' . $expectedValueLogLabel);

                    if (!$matchExpected && !$matchConverted) {
                        $fail = true;
                        $matched = false;

                        $this->log('  < FAIL');
                    } else {
                        $this->log('  < OK');

                        $matched = true;
                    }

                    $matches[$coord] = [
                        'value'     => $value,
                        'converted' => $convertedValue,
                        'expected'  => $expectedValueReportLabel,
                        'matched'   => $matched
                    ];

                    if ($setEncoding && !$encoding && $matchExpected) {
                        $encoding = 'utf-8';
                    }

                    if ($setEncoding && !$encoding && $matchConverted) {
                        $encoding = 'latin1';
                    }
                }

                return [
                    'matched'      => !$fail,
                    'matches'      => $matches,
                    'encoding'     => $encoding,
                    'sheet_index'  => $sheetIndex,
                    'sheets_names' => $encoding == 'latin1' ? $convertedSheetsNames : $sheetsNames
                ];
            } else {
                return [
                    'matched'     => false,
                    'matches'     => [],
                    'sheet_index' => null
                ];
            }
        } else {
            $this->log('EMPTY DETECT MAP importer ' . ($importer->name ?: '...') . ' (division: ' . $importer->division->name . ')');
        }
    }

    private function getAIPivot(\ss\multisource\models\InboxAttachment $attachment, \ss\multisource\models\Importer $importer)
    {
        $pivot = \ss\multisource\models\InboxAttachmentImporter::where('attachment_id', $attachment->id)
            ->where('importer_id', $importer->id)
            ->first();

        if (!$pivot) {
            $pivot = \ss\multisource\models\InboxAttachmentImporter::create([
                                                                                'attachment_id' => $attachment->id,
                                                                                'importer_id'   => $importer->id
                                                                            ]);
        }

        return $pivot;
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function getSpreadsheet($attachment)
    {
        $filePath = $this->_protected('data', '~:' . $attachment->file_path);
        $loadFilePath = $filePath;

        if (file_exists($filePath)) {
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);

            if ($ext == 'xlsx') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            if ($ext == 'xls') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }

            if ($ext == 'csv') {
                $pathinfo = pathinfo($filePath);

                $convertedFilePath = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-converted.' . $pathinfo['extension'];

                if (!file_exists($convertedFilePath)) {
                    try {
                        write($convertedFilePath, iconv('windows-1251', 'utf-8', read($filePath)));
                    } catch (\Throwable $e) {

                    }
                }

                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

                $loadFilePath = $convertedFilePath;
            }

            if (isset($reader)) {
                $spreadsheet = $reader->load($loadFilePath);

                return $spreadsheet;
            }
        } else {
            $this->log('attachment ' . $attachment->id . ' (' . $attachment->name . ') FILE NOT EXISTS');
        }
    }
}
