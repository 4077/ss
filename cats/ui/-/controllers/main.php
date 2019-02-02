<?php namespace ss\cats\ui\controllers;

class Main extends \Controller
{
    private $cat;

    private $d;

    private $s;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
            $this->instance_($this->cat->id);

            $this->d = $this->d(false, [
                'less_enabled' => true
            ]);

            $this->s = $this->s(false, [

            ]);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $ss = ss();

        $cat = $this->cat;

        $globalEditable = $ss->globalEditable();

        $catVisible = $cat->enabled && ($cat->published || $globalEditable);

        if (!$catVisible) {
            return null;
        }

        $this->app->html->setTitle($cat->meta_title ?: $ss->cats->getName($cat)); //
        $this->app->html->meta->set('keywords', $cat->meta_keywords); //
        $this->app->html->meta->set('description', $cat->meta_description); //

        $this->css('\std\images~');
        $this->css();
        $this->less();

        $v->assign([
                       'TREE_ID' => $cat->tree_id,
                       'CONTENT' => $this->wrap($this->render())
                   ]);

        $this->c('>editableCalls:run', ['cat' => $this->cat]);

        $this->widget(':', [
            '.r'    => [
                'reload'    => $this->_abs('>xhr:reload', [
                    'cat_id'      => $cat->id,
                    'multisource' => j64_($this->data('multisource'))
                ]),
                'pageClose' => $this->_abs('>xhr:pageClose', [
                    'cat_id' => $cat->id
                ]),
            ],
            'catId' => $cat->id
        ]);

        sstm()->events->trigger('cats/ui/pageOpen', [
            'cat' => $cat
        ]);

        return $v;
    }

    private function render()
    {
        $output = '';

        // renderers

        $ss = ss();

        $renderers = $ss->cats->getEnabledRenderers($this->cat);

        foreach ($renderers as $renderer) {
            $output .= $ss->cats->renderComponentPivot($renderer, 'ui', $this->data);
        }

        // containers

        $globalEditable = ss()->globalEditable();

        $containers = $this->cat->containers()->where('enabled', true)->orderBy('position')->get();

        foreach ($containers as $container) {
            if ($container->enabled && ($container->published || $globalEditable)) {
                $output .= $this->c('>container:view', [
                    'cat' => $container
                ], 'multisource')->render();
            }
        }

        return $output;
    }

    private function wrap($content)
    {
        $ss = ss();

        $wrappers = $ss->cats->getEnabledWrappers($this->cat);

        $data = $this->data;

        foreach ($wrappers as $wrapper) {
            ra($data, [
                'content' => $content
            ]);

            $content = $ss->cats->renderComponentPivot($wrapper, 'ui', $data);
        }

        return $content;
    }

    private function less()
    {
        if ($this->d['less_enabled']) {
            $lessNodes = ss()->cats->getLessNodes($this->cat);

            foreach ($lessNodes as $lessNode) {
                if ($lessNode) {
                    $this->css($lessNode, [
                        'treeId' => $this->cat->tree_id,
                        'catId'  => $this->cat->id
                    ]);
                }
            }
        }
    }
}
