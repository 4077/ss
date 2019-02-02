<?php namespace ss\cp\trees\tree\settings\controllers\main;

class App extends \Controller
{
    public function readData()
    {
        if ($tree = $this->unpackModel('tree')) {
            return _j($tree->data);
        }
    }

    public function writeData()
    {
        if ($tree = $this->unpackModel('tree')) {
            $tree->data = j_($this->data('data'));
            $tree->save();
        }
    }
}
