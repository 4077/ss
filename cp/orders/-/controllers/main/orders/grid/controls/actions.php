<?php namespace ss\cp\orders\controllers\main\orders\grid\controls;

class Actions extends \Controller
{
    private $buttons = [
        'delete' => [
            'label' => 'Удалить',
            'class' => 'delete',
            'path'  => '~orders/xhr:delete'
        ]
    ];

    public function view()
    {
        $v = $this->v();

        foreach ($this->buttons as $button) {
            $v->assign('button', [
                'CONTENT' => $this->c('\std\ui button:view', [
                    'path'    => $button['path'],
                    'data'    => ['order_id' => $this->data['order_id']],
                    'class'   => 'button ' . $button['class'],
                    'content' => '<div class="icon"></div>',
                    'attrs'   => ['title' => $button['label']]
                ])
            ]);
        }

        return $v;
    }
}
