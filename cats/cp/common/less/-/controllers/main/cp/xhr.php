<?php namespace ss\cats\cp\common\less\controllers\main\cp;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload', [], 'cat');
    }

    public function selectLessType()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->s('~:selected_less_type_by_cat_type/' . $cat->type, $this->data('type'), RR);

            $this->app->session->save($this->_module()->namespace);

            $this->c('~:reload', [
                'cat' => $cat
            ]);
        }
    }

    public function toggleEnabled()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $less = _j($cat->less);

            invert($less[$this->data('type')]['enabled']);

            $cat->less = j_($less);
            $cat->save();

            pusher()->trigger('ss/cat/' . $cat->id . '/less/toggle_enabled');
        }
    }

    public function toggleRewrite()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $less = _j($cat->less);

            invert($less[$this->data('type')]['rewrite']);

            $cat->less = j_($less);
            $cat->save();

            pusher()->trigger('ss/cat/' . $cat->id . '/less/toggle_rewrite');
        }
    }

    public function reset()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $selectedLessType = $this->s('~:selected_less_type_by_cat_type/' . $cat->type);

            $this->c('\ewma\nodeFileEditor~:reset|ss/cats/' . $cat->id . '/less/' . $selectedLessType);

            pusher()->trigger('ss/cat/' . $cat->id . '/less/reset');
        }
    }

    public function save()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $selectedLessType = $this->s('~:selected_less_type_by_cat_type/' . $cat->type);

            $this->c('\ewma\nodeFileEditor~:save|ss/cats/' . $cat->id . '/less/' . $selectedLessType);

            pusher()->trigger('ss/cat/' . $cat->id . '/less/save');
        }
    }

    public function saveAll()
    {
        if ($cat = $this->unxpackModel('cat')) {
            foreach (ss()->cats->getLessTypes($cat->type) as $type) {
                $this->c('\ewma\nodeFileEditor~:save|ss/cats/' . $cat->id . '/less/' . $type);
            }

            pusher()->trigger('ss/cat/' . $cat->id . '/less/save');
        }
    }

    public function updateCss()
    {
        $this->c('\ewma~cache:reset', ['cssCompiler' => true]);
        $this->c('\ewma~css:increaseVersion');

        $this->app->response->sendJson(['a' => -4]);
    }
}
