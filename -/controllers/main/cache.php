<?php namespace ss\controllers\main;

class Cache extends \Controller
{
    //
    // DROP views
    //

    public function dropAllViewsCache()
    {
        $path = abs_path('cache/ss/views');

        if (file_exists($path)) {
            delete_dir($path);

            return 'dropped path: ' . $path;
        } else {
            return 'path ' . $path . ' not exists';
        }
    }

    public function dropTreeViewsCache()
    {
        $treeId = $this->data('tree_id');

        $path = abs_path('cache/ss/views/tree_' . $treeId);

        if (file_exists($path)) {
            delete_dir($path);

            return 'dropped path: ' . $path;
        } else {
            return 'path ' . $path . ' not exists';
        }
    }

    public function dropPageViewsCache()
    {
        if ($page = \ss\models\Cat::find($this->data('cat_id'))) {
            $path = abs_path(
                'cache/ss/views',
                'tree_' . $page->tree_id,
                'cat_' . $page->id
            );

            if (file_exists($path)) {
                delete_dir($path);

                return 'dropped path: ' . $path;
            } else {
                return 'path ' . $path . ' not exists';
            }
        }
    }

    public function dropPageViewsCacheRecursive()
    {
        if ($page = \ss\models\Cat::find($this->data('cat_id'))) {
            $ids = \ewma\Data\Tree::getIds($page);

            foreach ($ids as $id) {
                $catCacheDirPath = abs_path(
                    'cache/ss/views',
                    'tree_' . $page->tree_id,
                    'cat_' . $id
                );

                delete_dir($catCacheDirPath);
            }

            return 'recursive drop for cat_id=' . $page->id;
        }
    }

    //
    // WARM views
    //

    public function warmTreeViewsCache()
    {
        $treeId = $this->data('tree_id');
        $sleepMs = $this->data('sleep_ms') ?: 1000;
        $pagesPerProcess = $this->data('pages_per_process') ?: 10;

        $pages = \ss\models\Cat::where('tree_id', $treeId)->where('type', 'page')->get();

        $ids = [];
        $i = 0;
        $n = 0;
        $pagesCount = count($pages);

        foreach ($pages as $page) {
            $i++;
            $n++;

            $ids[] = $page->id;

            if ($i == $pagesPerProcess || $n == $pagesCount) {
                $this->log('start warm pages: ' . a2l($ids));

                $process = $this->proc(':warmPagesCache', [
                    'ids'      => $ids,
                    'sleep_ms' => $sleepMs
                ])->run();

                $process->wait();

                $this->log('complete warm pages: ' . a2l($ids));

                $ids = [];
                $i = 0;

                usleep($sleepMs * 1000);
            }
        }
    }

    public function warmPagesCache()
    {
        $ids = $this->data('ids');
        $sleepMs = $this->data('sleep_ms') ?: 1000;

        $pages = \ss\models\Cat::whereIn('id', $ids)->get();

        foreach ($pages as $page) {
            $rootCat = ss()->trees->getRootCat($page->tree_id);

            $this->c('\ss\cats\ui~:view', [
                'cat' => $page
            ]);

            $this->c('\tdui\nav tree:view', [
                'root_cat' => $rootCat,
                'cat'      => $page
            ]);

            usleep($sleepMs * 1000);
        }

        return $ids;
    }

    public function warmPageCache()
    {
        if ($page = \ss\models\Cat::find($this->data('cat_id'))) {
            $rootCat = ss()->trees->getRootCat($page->tree_id);

            $this->c('\ss\cats\ui~:view', [
                'cat' => $page
            ]);

            $this->c('\tdui\nav tree:view', [
                'root_cat' => $rootCat,
                'cat'      => $page
            ]);
        }
    }

    public function warmViewsCache()
    {
        $treeId = $this->data('tree_id');
        $sleepMs = $this->data('sleep_ms');

        $rootCat = ss()->trees->getRootCat($treeId);

        $pages = \ss\models\Cat::where('tree_id', $treeId)->where('type', 'page')->get();

        start_time($this->_nodeId());

        $this->log('warm start for tree_id=' . $treeId . ' sleep_ms=' . $sleepMs);

        foreach ($pages as $page) {
            $this->c('\ss\cats\ui~:view', [
                'cat' => $page
            ]);

            $this->c('\tdui\nav tree:view', [
                'root_cat' => $rootCat,
                'cat'      => $page
            ]);

            usleep($sleepMs * 1000);
        }

        $duration = end_time($this->_nodeId(), true);

        $pagesCount = count($pages);

        $this->log('warm for tree_id=' . $treeId . ' completed in ' . $duration . ' ms, pages: ' . $pagesCount);

        return [
            'tree_id'     => $treeId,
            'duration'    => $duration,
            'pages_count' => $pagesCount
        ];
    }
}
