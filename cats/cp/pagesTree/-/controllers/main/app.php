<?php namespace ss\cats\cp\pagesTree\controllers\main;

class App extends \Controller
{
    public function treeQueryBuilder()
    {
        $cat = $this->unpackModel('cat');

        $builder = \ss\models\Cat::where('tree_id', $cat->tree_id)->where('type', 'page')->orderBy('position');

        return $builder;
    }

    public function moveCallback()
    {
        $cat = $this->data['cat'];

    }

    public function sortCallback()
    {
        $cat = $this->data['cat'];

    }
}
