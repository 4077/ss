<?php namespace ss;

class Svc extends \ewma\Service\Service
{
    /**
     * @var self
     */
    public static $instance;

    /**
     * @return \ss\Svc
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            $svc = new self;

            static::$instance = $svc;
            static::$instance->__register__();
        }

        return static::$instance;
    }

    protected $services = [
        'access',
        'auth',
        'trees',
        'cats',
        'search',
        'products',
        'own',
        'log',
        'logs',
        'mailer',
        'render',
        'events'
    ];

    /**
     * @var \ss\Svc\Access
     */
    public $access = \ss\Svc\Access::class;

    /**
     * @var \ss\Svc\Auth
     */
    public $auth = \ss\Svc\Auth::class;

    /**
     * @var \ss\Svc\Trees
     */
    public $trees = \ss\Svc\Trees::class;

    /**
     * @var \ss\Svc\Cats
     */
    public $cats = \ss\Svc\Cats::class;

    /**
     * @var \ss\Svc\Search
     */
    public $search = \ss\Svc\Search::class;

    /**
     * @var \ss\Svc\Products
     */
    public $products = \ss\Svc\Products::class;

    /**
     * @var \ss\Svc\Own
     */
    public $own = \ss\Svc\Own::class;

    /**
     * @var \ss\Svc\Log
     */
    public $log = \ss\Svc\Log::class;

    /**
     * @var \ss\Svc\Logs
     */
    public $logs = \ss\Svc\Logs::class;

    /**
     * @var \ss\Svc\Mailer
     */
    public $mailer = \ss\Svc\Mailer::class;

    /**
     * @var \ss\Svc\Render
     */
    public $render = \ss\Svc\Render::class;

    /**
     * @var \ss\Svc\Events
     */
    public $events = \ss\Svc\Events::class;

    //
    //
    //

    /**
     * @var \ewma\Controllers\Controller
     */
    public $moduleRootController;

    public $s;

    private $config;

    protected function boot()
    {
        $this->moduleRootController = app()->modules->getByNamespace('ss')->getController();

        $this->s = &ssc()->s(false, [
            'editable' => appc()->a('ss:*')
        ]);

        $this->config = handlers()->render('ss:config');
    }

    /**
     * @return \ewma\Controllers\Controller
     */
    public function c()
    {
        $args = func_get_args();

        if ($args) {
            $output = call_user_func_array([$this->moduleRootController, 'c'], $args);
        } else {
            $output = $this->moduleRootController;
        }

        return $output;
    }

    public function config($path = false)
    {
        return ap($this->config, $path);
    }

    public function globalEditable($value = null)
    {
        if (null === $value) {
            return appc()->a('ss:*') && $this->s['editable'];
        } else {
            $value &= appc()->a('ss:*');

            $this->s['editable'] = $value;
        }
    }
}
