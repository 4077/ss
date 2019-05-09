<?php namespace ss\multisource\ui\divisions\intersections\controllers;

class Main extends \Controller
{
    private $d;

    public function __create()
    {
        $this->d = $this->d(false, [
            'sources' => []
        ]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

//        $sources = \ss\multisource\models\Division::whereIn('id', $this->d['sources'])->orderBy('position')->get();
        $sources = \ss\multisource\models\Division::orderBy('position')->get();
        $targets = \ss\multisource\models\Division::orderBy('position')->get();

        foreach ($sources as $source) {
            $v->assign('column', [
                'NAME' => $source->name
            ]);
        }

        foreach ($targets as $target) {
            $v->assign('row', [
                'NAME' => $target->name
            ]);

            foreach ($sources as $source) {
                $intersection = ss()->multisource->divisionsIntersections->getIntersection($source, $target);

                $v->assign('row/column', [
                    'CONTROL' => $intersection
                        ? $this->c('>controls/edit:view', [
                            'intersection' => $intersection
                        ])
                        : $this->c('>controls/create:view', [
                            'source' => $source,
                            'target' => $target
                        ])
                ]);
            }
        }

        $v->assign([
                       'CONTENT' => false
                   ]);

        $this->css();

        return $v;
    }
}
