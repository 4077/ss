<?php namespace ss\products\cp\deleteAllConfirm\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        $this->cat = $this->unpackModel('cat');
    }

    public function reload()
    {
        $this->jquery()->replace($this->view(true));
    }

    public function view($reload = false)
    {
        $v = $this->v('|');

        $buttons = $this->getButtons();

        $s = &$this->s();

        if (!$reload) {
            ra($s, [
                'buttons_count'       => count($buttons),
                'last_pressed_number' => 0
            ]);
        }

        foreach ($buttons as $n => $label) {
            $v->assign('button', [
                'CONTENT' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:pressButton',
                    'data'    => [
                        'number' => $n + 1,
                        'cat'    => xpack_model($this->cat)
                    ],
                    'class'   => 'button ' . ($s['last_pressed_number'] > $n ? 'pressed' : ''),
                    'content' => $label
                ])
            ]);
        }

        $this->css(':\css\std~');

        return $v;
    }

    public function getButtons()
    {
        $count = $this->cat->products()->count();

        return [
            'Я уверен,',
            'что хочу удалить',
            'все <b>' . $count . '</b> товар' . ending($count, '', 'а', 'ов') . ' в категории <b>«' . ($this->cat->parent_id ? $this->cat->name : 'Без категории') . '»</b>',
            '!'
        ];
    }
}
