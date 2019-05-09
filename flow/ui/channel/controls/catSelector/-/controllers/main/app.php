<?php namespace ss\flow\ui\channel\controls\catSelector\controllers\main;

class App extends \Controller
{
    public function getQueryBuilder()
    {
        return \ss\models\Cat::where('tree_id', $this->data('tree_id'))->orderBy('type')->orderBy('position');
    }
}
