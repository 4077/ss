<?php namespace ss\Svc;

class Render extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    public function container($containerId)
    {
        if ($container = \ss\models\Cat::find($containerId)) {
            return appc('\ss\cats\ui~container:view', [
                'cat' => $container
            ]);
        }
    }
}
