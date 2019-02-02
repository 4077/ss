<?php namespace ss\suppliers\messages\controllers\main;

class ImportersDetector extends \Controller
{
    private $attachment;

    public function __create()
    {
        $this->attachment = $this->unpackModel('attachment') or $this->lock();
    }

    public function detect()
    {
        $this->attachment->importer = false;
        $this->attachment->save();

        if ($spreadsheet = $this->getSpreadsheet()) {
            $detectors = $this->data('detectors');

            $this->log('START detectors: ' . j_($detectors));

            foreach ($detectors as $detector) {
                $this->log('TRY detector: ' . $detector);

                $detected = $this->c($detector . ':detect', [
                    'spreadsheet' => $spreadsheet
                ]);

                if ($detected) {
                    $this->attachment->importer = $detected ?: '';
                    $this->attachment->save();

                    $this->log('attachment ' . $this->attachment->id . ' (' . $this->attachment->name . ') DETECTED importer: ' . $detected);

                    pusher()->trigger('ss/suppliers/messages/attachments/importerDetect');
                    pusher()->trigger('ss/suppliers/messages/id');

                    break;
                }
            }

            unset($spreadsheet);

            if (empty($detected)) {
                $this->log('attachment ' . $this->attachment->id . ' (' . $this->attachment->name . ') NOT DETECTED importer');

                pusher()->trigger('ss/suppliers/messages/attachments/importerDetect');
            }
        } else {
            $this->log('attachment ' . $this->attachment->id . ' (' . $this->attachment->name . ') NOT LOADED SPREADSHEET');
        }

        sleep(5);
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private function getSpreadsheet()
    {
        $filePath = $this->_protected('~:' . $this->attachment->file_path);

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
