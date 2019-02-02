<?php namespace ss\Svc;

class Log extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    public function write($message)
    {
        ssc('cp/log app:write', [
            'message' => $message
        ]);
    }

    public function read()
    {
        return implode('<br>', explode(PHP_EOL, ssc('cp/log app:read')));
    }
}
