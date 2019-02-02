<?php namespace ss\importLog\commander\panel\controllers\main\diff;

class App extends \Controller
{
    public function readDataBefore()
    {
        if ($change = $this->unpackModel('change')) {
            return _j($change->data_before);
        }
    }

    public function readDataAfter()
    {
        if ($change = $this->unpackModel('change')) {
            return _j($change->data_after);
        }
    }
}
