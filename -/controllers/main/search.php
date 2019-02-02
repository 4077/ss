<?php namespace ss\controllers\main;

class Search extends \Controller
{
    public function updateIndex()
    {
        $trees = \ss\models\Tree::all();

        foreach ($trees as $tree) {
            ss()->trees->updateSearchIndex($tree);
        }
    }

    public function updateTreeIndex()
    {
        if ($tree = \ss\models\Tree::find($this->data('tree_id'))) {
            return ss()->trees->updateSearchIndex($tree);
        }
    }

    public function updateCatIndex()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            ss()->cats->updateSearchIndex($cat);
        }
    }

    public function updateProductIndex()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            ss()->products->updateSearchIndex($product);
        }
    }

    public function analyzeLog()
    {
        $filePath = abs_path('logs/search_requests.log');

        $requests = file($filePath);
        $requests = array_unique($requests);

        $prevIp = false;

        $queries = [];

        $queriesByIp = [];

        foreach ($requests as $request) {
            preg_match('/(\d+\.\d+\.\d+\.\d+).*value=(.*)/', $request, $match);

            $ip = $match[1];

            if ($ip != $prevIp) {
                $prevIp = $ip;

                $queriesByIp = [];
            }

            $query = mb_strtolower(str_replace('\\ ', ' ', $match[2]));

            if (!in_array($query, $queriesByIp)) {
                if (!isset($queries[$query])) {
                    $queries[$query] = 0;
                }

                $queries[$query]++;
            }

            $queriesByIp[] = $query;
        }

        arsort($queries);

        $filePath = abs_path('logs/queries.txt');

        $file = fopen($filePath, 'w');

        foreach ($queries as $query => $count) {
            fwrite($file, $count . "\t" . $query . "\n");
        }

        fclose($file);

        return count($queries);
    }

    public function resetCache()
    {
        ss()->search->cache->reset();
    }
}
