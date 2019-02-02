<?php namespace ss\products\cp\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function create()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $cat->products()->create([]);

            $this->e('ss/products/create')->trigger(['cat' => $cat]);
        }
    }

    public function deleteAll()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\std\ui\dialogs~:open:deleteAllConfirm|ss/cats', [
                'path' => 'deleteAllConfirm~:view',
                'data' => [
                    'cat' => pack_model($cat)
                ]
            ]);

            $this->e('ss/products/delete_all')->rebind('\std\ui\dialogs~:close:deleteAllConfirm');
        }
    }
}
