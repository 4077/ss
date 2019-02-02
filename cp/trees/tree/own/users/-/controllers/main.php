<?php namespace ss\cp\trees\tree\own\users\controllers;

class Main extends \Controller
{
    private $tree;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();
    }

    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $s = $this->s(false, [
            'page'         => 1,
            'per_page'     => 10,
            'login_filter' => ''
        ]);

        $v->assign([
                       'USERS_LIST'         => $this->c('>usersList:view', [
                           'tree' => $this->tree
                       ]),
                       'LOGIN_FILTER_VALUE' => $s['login_filter']
                   ]);

        $this->c('\std\ui liveinput:bind', [
            'selector' => $this->_selector('. .login_filter input'),
            'path'     => '>xhr:updateLoginFilter',
            'timeout'  => 100
        ]);

        $this->css();

        return $v;
    }
}
