<?php namespace ss\cats\cp\controllers\main;

class App extends \Controller
{
    public function onCatSelect()
    {
        $cat = $this->data['cat'];

        $this->s('~:selected_cat_id|', $cat->id, RR);

        $this->c('~:reload|');
    }

    public function onCatCreate()
    {
        $cat = $this->data['cat'];

        $this->s('~:selected_cat_id|', $cat->id, RR);

        $this->c('~:reload|');
    }

    public function onCatDelete()
    {
        $this->c('~:reload|');
    }
}
