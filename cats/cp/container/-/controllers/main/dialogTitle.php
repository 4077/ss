<?php namespace ss\cats\cp\container\controllers\main;

class DialogTitle extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
            $this->instance_($this->cat->id);
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

        $cat = $this->cat;

        $class = [];

        if (!$cat->enabled) {
            $class[] = 'disabled';
        }

        if (!$cat->published) {
            $class[] = 'not_published';
        }

        $v->assign([
                       'CLASS' => implode(' ', $class),
                       'NAME'  => ss()->cats->getShortName($cat)
                   ]);

        $this->css();

        $this->widget(':|', [
            'catId' => $cat->id
        ]);

        return $v;
    }
}
