<?php namespace ss\multisource\ui\division\importersControls\treeSelector\controllers\main\tree;

class App extends \Controller
{
    public function treeQueryBuilder()
    {
        return \ss\models\Tree::orderBy('position');
    }
}
