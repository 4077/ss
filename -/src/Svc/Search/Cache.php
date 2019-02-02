<?php namespace ss\Svc\Search;

class Cache extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    private function getCacheFilePath($path)
    {
        return appc('\ss~search')->_protected('cache', $path . '.php');
    }

    private $cache = [];

    public function get($path)
    {
        if (!isset($this->cache[$path])) {
            $cacheFilePath = $this->getCacheFilePath($path);

            if (file_exists($cacheFilePath)) {
                $this->cache[$path] = aread($cacheFilePath);
            } else {
                $this->cache[$path] = [];

                awrite($cacheFilePath, []);
            }
        }

        return $this->cache[$path];
    }

    public function update($path, $data)
    {
        $cacheFilePath = $this->getCacheFilePath($path);

        awrite($cacheFilePath, $data);
    }

    public function reset()
    {
        delete_dir(appc('\ss~search')->_protected('cache', false));
    }
}
