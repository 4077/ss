<?php namespace ss\multisource;

class Ui
{
    public static $instance;

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new self;
        }

        return static::$instance;
    }

    public $mainController;

    public function __construct()
    {
        $this->mainController = appc('\ss\multisource\ui~');
    }

    //
    //
    //

    public function setBaseRoute($baseRoute)
    {
        $this->mainController->d(':base_route', $baseRoute, RR);
    }

    public function getBaseRoute()
    {
        return $this->mainController->d(':base_route');
    }

    public function getRoute($tail)
    {
        return force_slashes(path($this->getBaseRoute(), $tail));
    }
}
