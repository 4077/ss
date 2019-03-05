<?php namespace ss\cats\controllers\main\viewsCache;

class Proc extends \Controller
{
    public function warmTree()
    {
        $appProcess = $this->app->process;

        $treeId = $this->data('tree_id');
        $sleepMs = $this->data('sleep_ms');
        $pagesPerProcess = $this->data('pages_per_process');

        $pages = \ss\models\Cat::where('tree_id', $treeId)->where('type', 'page')->get();

        $ids = [];
        $i = 0;
        $n = 0;
        $pagesCount = count($pages);

        foreach ($pages as $page) {
            if ($appProcess->handleIteration()) {
                break;
            }

            $i++;
            $n++;

            $ids[] = $page->id;

            if ($i == $pagesPerProcess || $n == $pagesCount) {
                $this->log('start warm pages: ' . a2l($ids));

                $process = $this->proc(':warmCats', [
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

    public function warmCats()
    {
        $appProcess = $this->app->process;

        $ids = $this->data('ids');
        $sleepMs = $this->data('sleep_ms');

        $cats = \ss\models\Cat::whereIn('id', $ids)->get();

        foreach ($cats as $cat) {
            if ($appProcess->handleIteration()) {
                break;
            }

            $this->c('\ss\cats\ui~:view', [
                'cat' => $cat
            ]);

            $this->se('ss/cache/views/warm/page')->trigger(['cat' => $cat]);

            usleep($sleepMs * 1000);
        }

        return $ids;
    }
}
