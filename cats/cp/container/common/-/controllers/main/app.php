<?php namespace ss\cats\cp\container\common\controllers\main;

class App extends \Controller
{
    public function getFields()
    {
        return l2a('short_name, name, description');
    }

    public function tileUpdate()
    {
        $pivot = $this->data['pivot'];

        $this->c('~:reloadTile', [
            'cat' => $pivot->cat
        ]);
    }
}
