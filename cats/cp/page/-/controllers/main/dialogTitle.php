<?php namespace ss\cats\cp\page\controllers\main;

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

        $catType = $cat->tree->mode == 'folders' ? 'folder' : 'page'; // todo page>folder

        $class[] = $catType;

        $v->assign([
                       'CLASS' => implode(' ', $class),
                       'NAME'  => ss()->cats->getShortName($cat),
                       'ICON'  => $catType == 'page' ? 'fa fa-file' : 'fa fa-folder'
                   ]);

        $this->css();

        $this->widget(':|', [
            'catId' => $cat->id
        ]);

        return $v;
    }
}
