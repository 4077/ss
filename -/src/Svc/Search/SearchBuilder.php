<?php namespace ss\Svc\Search;

class SearchBuilder
{
    /**
     * @var \ss\Svc\Search
     */
    private $svc;

    public function __construct($svc)
    {
        $this->svc = $svc;
    }

    private $trees;

    public function allTrees()
    {
        $this->trees = false;

        return $this;
    }

    public function tree($treeId)
    {
        $this->trees = [$treeId];

        return $this;
    }

    public function trees($treesIds)
    {
        $this->trees = $treesIds;

        return $this;
    }

    private $publishedOnly = false;

    public function publishedOnly($value = true)
    {
        $this->publishedOnly = $value;

        return $this;
    }

    private $enabledOnly = false;

    public function enabledOnly($value = true)
    {
        $this->enabledOnly = $value;

        return $this;
    }

    private $offset = 0;

    public function offset($value)
    {
        $this->offset = $value;

        return $this;
    }

    private $take = 50;

    public function take($value)
    {
        $this->take = $value;

        return $this;
    }

    private $query;

    private $cacheKey;

    public function query($query)
    {
        $this->query = str_replace(['"', '\''], '', trim($query));
    }

    public function search($query = false)
    {
        if ($query) {
            $this->query($query);
        }

        $this->cacheKey = jmd5([
                                   $this->query,
                                   $this->publishedOnly,
                                   $this->enabledOnly,
                                   $this->trees
                               ]);

//        start_time('RPL');
        $products = $this->renderProductsList();

//        appc()->console(end_time('RPL'));

        return $products;
    }

    private function renderProductsList()
    {
        $cachePath = 'product_ids/' . $this->cacheKey;

        $svc = $this->svc;

        $cache = $svc->cache->get($cachePath);

        if (empty($cache)) {
            $levels = [];

            $fullWords = $svc->query->getFullWords($this->query);

            // полное вхождение каждого слова
            foreach ($fullWords as $word) {
                $levels[0][$word] = table_ids($this->getProductsBuilder()->where('search_index', 'like', '% ' . $word . ' %')->get());
            }

            // вхождение каждого от начала
            foreach ($fullWords as $word) {
                $levels[1][$word] = table_ids($this->getProductsBuilder()->where('search_index', 'like', '% ' . $word . '%')->get());
            }

            $trimmedWords = $svc->query->getTrimmedWords($this->query);

            // вхождение каждого обрезка от начала
            foreach ($trimmedWords as $word) {
                $levels[2][$word] = table_ids($this->getProductsBuilder()->where('search_index', 'like', '% ' . $word . '%')->get());
            }

            // любое вхождение каждого обрезка
            foreach ($trimmedWords as $word) {
                $levels[3][$word] = table_ids($this->getProductsBuilder()->where('search_index', 'like', '%' . $word . '%')->get());
            }

            $levelWeight = [40, 20, 10, 5];

            $weights = [];

            for ($level = 0; $level < 4; $level++) {
                foreach ($levels[$level] as $word => $ids) {
                    foreach ($ids as $id) {
                        $weight = $weights[$id] ?? 0;

                        $weights[$id] = $weight + $levelWeight[$level];
                    }
                }
            }

            arsort($weights);

            $cache = array_keys($weights);

            $svc->cache->update($cachePath, $cache);
        }

        $loadIds = array_slice($cache, $this->offset, $this->take);

        $products = map(table_rows_by_id(\ss\models\Product::whereIn('id', $loadIds)->get()), $loadIds);

        return $products;
    }

    private $productsBuilderPrototype;

    private function getProductsBuilder()
    {
        if (null === $this->productsBuilderPrototype) {
            $builder = \ss\models\Product::where('searchable', true);

            if ($this->trees) {
                $builder = $builder->whereIn('tree_id', $this->trees);
            } else {
                $builder = $builder->where('tree_id', '!=', 0);
            }

            if ($this->enabledOnly) {
                $builder = $builder->where('enabled', true);
            }

            if ($this->publishedOnly) {
                $builder = $builder->where('published', true);
            }

            // todo branch_published

            $this->productsBuilderPrototype = $builder;

            /*            $this->productsBuilderPrototype = \ss\models\Product::where('tree_id', 2)
                            ->where('searchable', true)
                            ->where('enabled', true)
                            ->where('published', true)
                            ->whereHas('cat', function ($query) {
                                $query->where('enabled', true)->where('published', true)->whereHas('parent', function ($query) {
                                    $query->where('enabled', true);
                                    $query->where('published', true);
                                });
                            });*/
        }

        return clone $this->productsBuilderPrototype;
    }
}
