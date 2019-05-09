<?php namespace ss\flow\ui\channels\controls\treeSelector\controllers\main\tree;

class App extends \Controller
{
    public function treeQueryBuilder()
    {
        return \ss\models\Tree::orderBy('position');
    }
}
