<?php namespace ss\Svc;

class Logs extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    public function write($fileName, $row)
    {
        $filePath = ssc()->_protected('logs', $fileName);
        $dirPath = dirname($filePath);

        mdir($dirPath);

        $file = fopen($filePath, 'a+');

        fwrite($file, $row . PHP_EOL);

        fclose($file);
    }

    public function read($fileName)
    {
        $filePath = ssc()->_protected('logs', $fileName);

        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }
    }
}
