<?php namespace ss\cats\ui\cpanel\controllers;

class Main extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $s = $this->s('|', [
            'buttons_visible' => false
        ]);

        $globalEditable = ss()->globalEditable();

        $v->assign([
                       'EDITABLE_CLASS'                => $globalEditable ? 'editable' : '',
                       'GLOBAL_EDITABLE_TOGGLE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:toggleGlobalEditable',
                           'data'  => [

                           ],
                           'class' => 'global_editable_toggle_button ' . ($globalEditable ? 'pressed' : ''),
                           'icon'  => $globalEditable ? 'fa fa-pencil' : 'fa fa-eye',
                           //                           'title' => $globalEditable ? 'Выключить редактирование' : 'Включить редактирование'
                       ]),
                   ]);

        foreach ($this->getButtons() as $button) {
            remap($buttonData, $button, 'visible, path, data, class, icon, title');

            $v->assign('button', [
                'CONTENT' => $this->c('\std\ui button:view', $buttonData)
            ]);
        }

        $this->css();

        $this->widget(':|', [
            '.e'             => [
                'ss/cpanel/buttons_toggle' => 'buttonsToggle'
            ],
            '.r'             => [
                'setButtonsVisible' => $this->_p('>xhr:setButtonsVisible|')
            ],
            'buttonsVisible' => $s['buttons_visible'],
            'editable'       => $globalEditable
        ]);

        return $v;
    }

    private function getButtons()
    {
        return [
            [
                'path'  => $this->_p('>xhr:pagesTreeDialog'),
                'data'  => ['cat' => xpack_model($this->data('cat'))],
                'class' => 'tree button',
                'icon'  => 'fa fa-tree',
                'label' => ''
            ],
            [
                'path'  => $this->_p('>xhr:pageNodeDialog'),
                'data'  => ['cat' => xpack_model($this->data('cat'))],
                'class' => 'tree button',
                'icon'  => 'fa fa-cube',
                'label' => ''
            ],
            [
                'path'  => $this->_p('>xhr:pageDialog'),
                'class' => 'page button',
                'icon'  => 'fa fa-cog',
                'label' => ''
            ]
        ];
    }
}
