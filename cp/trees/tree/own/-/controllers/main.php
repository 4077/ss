<?php namespace ss\cp\trees\tree\own\controllers;

class Main extends \Controller
{
    private $s;

    private $tree;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();

        $this->s = $this->s('|', [
            'selected_id' => false
        ]);
    }

    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $this->s(false, [
            'selected_user_id' => false
        ]);

        $v->assign([
                       'USERS' => $this->c('users~:view|', [
                           'tree' => $this->tree
                       ]),
                       'CATS'  => $this->c('cats~:view|', [
                           'tree' => $this->tree
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
