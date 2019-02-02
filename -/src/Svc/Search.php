<?php namespace ss\Svc;

class Search extends \ewma\Service\Service
{
    protected $services = ['svc', 'cache', 'query'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    /**
     * @var \ss\Svc\Search\Cache
     */
    public $cache = \ss\Svc\Search\Cache::class;

    /**
     * @var \ss\Svc\Search\Query
     */
    public $query = \ss\Svc\Search\Query::class;

    //
    //
    //

    public function new()
    {
        return new \ss\Svc\Search\SearchBuilder($this);
    }
}
