<?php namespace ss\cats\cp\page\common\controllers\main;

class App extends \Controller
{
    public function getFields()
    {
        return l2a('short_name, name, description, alias, meta_title, meta_keywords, meta_description');
    }

    public function imagesUpdate()
    {
        $cat = $this->data('cat');

        $cat->images_cache = '';
        $cat->save();

        $imagesCount = $cat->images()->count();

//        $this->se('ss/cat/any/update_images')->trigger(['cat_id' => $cat->id]);

        pusher()->trigger('ss/page/update', [
            'id'     => $cat->id,
            'images' => [
                'has'   => (bool)$imagesCount,
                'count' => $imagesCount
            ]
        ]);

        pusher()->trigger('ss/page/update', [
            'id'           => $cat->id,
            'imagesOthers' => true
        ]);
    }
}
