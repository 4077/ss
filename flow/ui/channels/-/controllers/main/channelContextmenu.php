<?php namespace ss\flow\ui\channels\controllers\main;

class ChannelContextmenu extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CONTENT' => false
                   ]);

        $this->css();

        $this->widget(':|', [
            '.r' => [
                'delete' => $this->_p('>xhr:delete')
            ]
        ]);

        return $v;
    }
}
