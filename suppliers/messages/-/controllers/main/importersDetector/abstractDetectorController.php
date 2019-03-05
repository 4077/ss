<?php namespace ss\suppliers\messages\controllers\main\importersDetector;

abstract class AbstractDetectorController extends \Controller
{
    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $spreadsheet;

    public function __create()
    {
        $this->spreadsheet = $this->data('spreadsheet');
    }

    abstract public function detect();

    public function checkCellValues($map, $worksheet = false)
    {
        if (!$worksheet) {
            $worksheet = $this->spreadsheet->getActiveSheet();
        }

        $encoding = false;

        foreach ($map as $coordinate => $expectedValue) {
            $value = (string)$worksheet->getCell($coordinate);

            if (empty($value)) {
                $convertedValue = $value;
            } else {
                if (!$convertedValue = @iconv('cp1251', 'utf-8', iconv('', 'latin1', $value))) {
                    $convertedValue = @mb_convert_encoding(mb_convert_encoding($value, 'latin1', ''), 'utf-8', 'cp1251');
                }
            }

            $this->log('value: ' . $value);
            $this->log('converted: ' . $convertedValue);
            $this->log('expected: ' . $expectedValue);

            $matchExpected = $value == $expectedValue;
            $matchConverted = $convertedValue == $expectedValue;

            if (!$matchExpected && !$matchConverted) {
                return false;
            }

            if (!$encoding && $matchExpected) {
                $encoding = 'utf-8';
            }

            if (!$encoding && $matchConverted) {
                $encoding = 'latin1';
            }
        }

        return $encoding;
    }

    public function getOutput($importer, $encoding)
    {
        return [
            'importer' => $importer,
            'encoding' => $encoding
        ];
    }
}
