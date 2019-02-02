<?php namespace ss\cats\cp\common\handler\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {

        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->cat->id)->replace($this->view());
    }

    public function view()
    {
        $cat = $this->cat;

        $v = $this->v('|' . $cat->id);

        $handler = ss()->cats->getOrCreateHandler($cat);

        $enabled = $cat->handler_enabled;

        $v->assign([
                       'HANDLER'               => $this->c('\ewma\handlers\ui\handler~:view', [
                           'handler' => $handler
                       ]),
                       'TOGGLE_ENABLED_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => [
                               'cat' => xpack_model($cat)
                           ],
                           'class'   => 'toggle_enabled button ' . ($enabled ? 'enabled' : ''),
                           'content' => $enabled ? 'Включен' : 'Выключен'
                       ]),
                       'COMPILE_BUTTON'        => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:compile',
                           'data'    => [
                               'handler' => xpack_model($handler)
                           ],
                           'class'   => 'compile button',
                           'content' => 'Скомпилировать'
                       ])
                   ]);

        $this->css(':\css\std~');

        return $v;
    }
}
