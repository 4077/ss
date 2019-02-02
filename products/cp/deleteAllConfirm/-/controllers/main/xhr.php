<?php namespace ss\products\cp\deleteAllConfirm\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function pressButton()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $s = &$this->s('~');

            if ($this->data('number') == $s['last_pressed_number'] + 1) {
                $s['last_pressed_number']++;

                if ($s['last_pressed_number'] == $s['buttons_count']) {
                    $cat->products()->delete();

                    $this->c('\std\ui\dialogs~:close:deleteAllConfirm|ss/cp/products');
//                    $this->e('ss/products/delete_all', ['cat_id' => $cat->id])->trigger();
                    $this->e('ss/products/delete_all')->trigger(['cat' => $cat]);

//                    $this->e('ss/cats/update')->trigger(['cat' => $cat]);
                }
            } else {
                $s['last_pressed_number'] = 0;
            }

            $this->c('~:reload', [], 'cat');
        }
    }
}
