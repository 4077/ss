<?php namespace ss\cats\controllers\main;

class ViewsCache extends \Controller
{
    //
    // DROP
    //

    public function dropAll()
    {
        $path = abs_path('cache/ss/views');

        if (file_exists($path)) {
            delete_dir($path);

            return 'dropped path: ' . $path;
        } else {
            return 'path ' . $path . ' not exists';
        }
    }

    public function dropTree()
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

    public function dropCat()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $path = abs_path(
                'cache/ss/views',
                'tree_' . $cat->tree_id,
                'cat_' . $cat->id
            );

            if (file_exists($path)) {
                delete_dir($path);

                return 'dropped path: ' . $path;
            } else {
                return 'path ' . $path . ' not exists';
            }
        }
    }

    public function dropCatRecursive()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $ids = \ewma\Data\Tree::getIds($cat);

            foreach ($ids as $id) {
                $catCacheDirPath = abs_path(
                    'cache/ss/views',
                    'tree_' . $cat->tree_id,
                    'cat_' . $id
                );

                delete_dir($catCacheDirPath);
            }

            return 'recursive drop for cat_id=' . $cat->id;
        }
    }

    //
    // WARM
    //

    public function warmTree()
    {
        $this->d(false, [
            'tree_warming_pid' => false
        ]);

        $treeId = $this->data('tree_id');
        $sleepMs = $this->data('sleep_ms') ?: 1000;
        $pagesPerProcess = $this->data('pages_per_process') ?: 10;

        $process = $this->proc('>proc:warmTree', [
            'tree_id'           => $treeId,
            'sleep_ms'          => $sleepMs,
            'pages_per_process' => $pagesPerProcess
        ])->run();

        $this->d(':tree_warming_pid', $process->getPid(), RR);

        $this->app->storage->save($this->_module()->namespace);

        $process->wait();

        $this->d(':tree_warming_pid', false, RR);
    }

    public function breakTreeWarming()
    {
        if ($pid = $this->d(':tree_warming_pid')) {
            $process = $this->app->process->dispatcher->open($pid);

            $process->break();
        }
    }

    public function warmPage()
    {
        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            if ($cat->type == 'page') {
                $this->c('\ss\cats\ui~:view', [
                    'cat' => $cat
                ]);

                $this->se('ss/cache/views/warm/page')->trigger(['cat' => $cat]);
            } else {
                return 'cat ' . $cat . ' type is ' . $cat->type . ', must be page';
            }
        }
    }
}
