<?php namespace ss\cats\cp\common\less\controllers\main;

class App extends \Controller
{
    public function onSave()
    {

    }

    public function onReset()
    {

    }

    public function onUpdate()
    {
        $cat = $this->unpackModel('cat');

        pusher()->triggerOthers('ss/cat/' . $cat->id . '/less/update');
        pusher()->triggerSelf('ss/cat/' . $cat->id . '/less/update-self');
    }
}
