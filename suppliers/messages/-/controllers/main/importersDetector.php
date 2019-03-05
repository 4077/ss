<?php namespace ss\suppliers\messages\controllers\main;

class ImportersDetector extends \Controller
{
    private $attachment;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $spreadsheet;

    public function __create()
    {
        $this->attachment = $this->unpackModel('attachment') or $this->lock();
        $this->spreadsheet = $this->getSpreadsheet();
    }

    public function detect()
    {
        $this->attachment->importer_name = '';
        $this->attachment->importer_handler = false;
        $this->attachment->save();

        if ($this->spreadsheet) {
            $detectors = $this->data('detectors');

            $this->log('START detectors: ' . a2l(array_keys($detectors)));

            foreach ($detectors as $detectorName => $detectorData) {
                $detected = $this->tryDetector($detectorName, $detectorData);

                if ($detected) {
                    $this->attachment->importer_name = $detected['importer_name'];
                    $this->attachment->importer_handler = $detected['importer_handler'];
                    $this->attachment->encoding = $detected['encoding'];
                    $this->attachment->save();

                    $this->log('attachment ' . $this->attachment->id . ' (' . $this->attachment->name . ') DETECTED importer: ' . $detected['importer_name'] . ', encoding: ' . $detected['encoding']);

                    pusher()->trigger('ss/suppliers/messages/attachments/importerDetect');
                    pusher()->trigger('ss/suppliers/messages/id');

                    break;
                }
            }

            unset($this->spreadsheet);

            if (empty($detected)) {
                $mailer = mailer('mailers:dev');

                $recipients = handlers()->render('ss/mail-recipients:suppliers-events');

                foreach ($recipients as $recipient) {
                    $mailer->addAddress($recipient);
                }

                $mailer->Subject = 'Не определенный прайс-лист от ' . $this->attachment->message->from;
                $mailer->Body = implode('<br>', [
                    dt(),
                    'Имя файла: ' . $this->attachment->name
                ]);

                $mailer->addAttachment($this->getAttachmentFilePath());

                $mailer->queue();

                $this->log('attachment ' . $this->attachment->id . ' (' . $this->attachment->name . ') NOT DETECTED importer');

                pusher()->trigger('ss/suppliers/messages/attachments/importerDetect');
            }
        } else {
            $this->log('attachment ' . $this->attachment->id . ' (' . $this->attachment->name . ') NOT LOADED SPREADSHEET');
        }
    }

    public function tryDetector($name, $data)
    {
        $this->log('TRY detector: ' . $name);

        $worksheet = $this->spreadsheet->getActiveSheet();

        $encoding = false;

        foreach ($data['map'] as $coordinate => $expectedValue) {
            $expectedValue = str_replace('\n', "\n", $expectedValue);

            $value = (string)$worksheet->getCell($coordinate);

            if (empty($value)) {
                $convertedValue = $value;
            } else {
                if (!$convertedValue = @iconv('cp1251', 'utf-8', iconv('', 'latin1', $value))) {
                    $convertedValue = @mb_convert_encoding(mb_convert_encoding($value, 'latin1', ''), 'utf-8', 'cp1251');
                }
            }

            $this->log('test cell: '.$coordinate);
            $this->log('  > value: ' . $value);
            $this->log('  > converted: ' . $convertedValue);
            $this->log('  > expected: ' . $expectedValue);

            $matchExpected = $value == $expectedValue;
            $matchConverted = $convertedValue == $expectedValue;

            if (!$matchExpected && !$matchConverted) {
                $this->log('  < FAIL');

                return false;
            }

            $this->log('  < OK');

            if (!$encoding && $matchExpected) {
                $encoding = 'utf-8';
            }

            if (!$encoding && $matchConverted) {
                $encoding = 'latin1';
            }
        }

        $importerHandlerOutput = handlers()->render($data['importer']);

        return [
            'importer_name'    => $importerHandlerOutput['name'] ?? '-',
            'importer_handler' => $data['importer'],
            'encoding'         => $encoding
        ];
    }

    private function getAttachmentFilePath()
    {
        return $this->_protected('~:' . $this->attachment->file_path);
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function getSpreadsheet()
    {
        $filePath = $this->getAttachmentFilePath();

        if (file_exists($filePath)) {
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);

            if ($ext == 'xlsx') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }

            if ($ext == 'xls') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            }

            if (isset($reader)) {
                $spreadsheet = $reader->load($filePath);

                return $spreadsheet;
            }
        } else {
            $this->log('attachment ' . $this->attachment->id . ' (' . $this->attachment->name . ') FILE NOT EXISTS');
        }
    }
}
