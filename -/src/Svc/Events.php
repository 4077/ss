<?php namespace ss\Svc;

class Events extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    /**
     * @var \ss\controllers\main\Events
     */
    private $eventsController;

    public function boot()
    {
        $this->eventsController = appc('\ss~events');
    }

    public function bind($path, $data = [])
    {
        $this->eventsController->bind($path, $data);
    }

    public function trigger($path, $data = [])
    {
        $this->eventsController->trigger($path, $data);
    }
}
