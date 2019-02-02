<?php namespace ss\cats\cp\common\data\controllers\main;

class App extends \Controller
{
    public function readData()
    {
        $cat = $this->unpackModel('cat');

        return _j($cat->data);
    }

    public function writeData()
    {
        $cat = $this->unpackModel('cat');

        $cat->data = j_($this->data('data'));
        $cat->save();
    }
}
