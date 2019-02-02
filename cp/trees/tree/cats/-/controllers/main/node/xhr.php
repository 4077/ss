<?php namespace ss\cp\trees\tree\cats\controllers\main\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->s('~:selected_id|', $cat->id, RR);

            $this->c('~:reload|', [
                'tree' => $cat->tree
            ]);
        }
    }

    // todo переделать биндинги для закомментированных:

    public function createFolder()
    {
        if ($cat = $this->unxpackModel('cat')) {
            ss()->cats->createFolder($cat, ['enabled' => true]);

//            pusher()->trigger('ss/page/' . $cat->id . '/update_pages');
//            pusher()->trigger('ss/tree/' . $cat->tree_id . '/update_pages');

            pusher()->trigger('ss/cat/update_cats', [
                'id' => $cat->id
            ]);

            pusher()->trigger('ss/tree/update_cats', [
                'id' => $cat->tree_id
            ]);
        }
    }

    public function createPage()
    {
        if ($cat = $this->unxpackModel('cat')) {
            ss()->cats->createPage($cat, ['enabled' => true]);

//            pusher()->trigger('ss/page/' . $cat->id . '/update_pages');
//            pusher()->trigger('ss/tree/' . $cat->tree_id . '/update_pages');

            pusher()->trigger('ss/cat/update_cats', [
                'id' => $cat->id
            ]);

            pusher()->trigger('ss/tree/update_cats', [
                'id' => $cat->tree_id
            ]);
        }
    }

    public function createContainer()
    {
        if ($cat = $this->unxpackModel('cat')) {
            ss()->cats->createContainer($cat);

//            pusher()->trigger('ss/page/' . $cat->id . '/update_containers');
//            pusher()->trigger('ss/tree/' . $cat->tree_id . '/update_containers');

            pusher()->trigger('ss/cat/update_cats', [
                'id' => $cat->id
            ]);

            pusher()->trigger('ss/tree/update_cats', [
                'id' => $cat->tree_id
            ]);
        }
    }

    public function delete()
    {

    }
}
