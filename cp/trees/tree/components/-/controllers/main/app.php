<?php namespace ss\cp\trees\tree\components\controllers\main;

class App extends \Controller
{
    public function getQueryBuilder()
    {
        return \ewma\components\models\Cat::orderBy('position');
    }
}
