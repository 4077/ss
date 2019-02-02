<?php namespace ss\cp\trees\tree\cats\controllers\main;

class App extends \Controller
{
    public function treeQueryBuilder()
    {
        return \ss\models\Cat::where('tree_id', $this->data('tree_id'))->orderBy('type')->orderBy('position');
    }
}
