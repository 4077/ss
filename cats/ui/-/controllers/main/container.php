<?php namespace ss\cats\ui\controllers\main;

class Container extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
            $this->instance_($this->cat->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $ss = ss();

        $cat = $this->cat;

        if ($ss->globalEditable() && $cat->output_enabled) {
            $v->assign('cp');

            $v->assign('not_published_mark', [
                'HIDDEN_CLASS' => $cat->published ? 'hidden' : ''
            ]);

            if ($ss->cats->isEditable($cat)) {
                $v->assign('CONTAINER_DIALOG_BUTTON', $this->c('\std\ui button:view', [
                    'path'  => '>xhr:containerDialog',
                    'data'  => [
                        'cat' => xpack_model($cat)
                    ],
                    'class' => 'container_dialog button',
                    'icon'  => 'fa fa-cog'
                ]));
            }

            $v->assign('EDITABLE_CLASS', 'editable');
        }

        $this->css();
        $this->less();

        $content = $this->wrap($this->render());

        if ($cat->output_enabled) {
            $v->assign('CONTENT', $content);
        }

        $this->widget(':|', [
            '.e'    => [
                'ss/container/' . $cat->id . '/update_pivot' => 'mr.reload'
            ],
            '.r'    => [
                'reload' => $this->_abs('>xhr:reload', [
                    'cat'         => xpack_model($cat),
                    'multisource' => j64_($this->data('multisource'))
                ])
            ],
            'catId' => $cat->id
        ]);

        return $v;
    }

    private function render()
    {
        $output = '';

        $ss = ss();

        $renderers = $ss->cats->getEnabledRenderers($this->cat);

        foreach ($renderers as $renderer) {
            $output .= $ss->cats->renderComponentPivot($renderer, 'ui', $this->data);
        }

        return $output;
    }

    private function wrap($content)
    {
        $ss = ss();

        $wrappers = $ss->cats->getEnabledWrappers($this->cat);

        $data = $this->data;

        foreach ($wrappers as $wrapper) {
            ra($data, [
                'content' => $content
            ]);

            $content = $ss->cats->renderComponentPivot($wrapper, 'ui', $data);
        }

        return $content;
    }

    public function less()
    {
        if ($this->d('~:less_enabled')) {
            $lessNodes = ss()->cats->getLessNodes($this->cat);

            foreach ($lessNodes as $lessNode) {
                if ($lessNode) {
                    $this->css($lessNode, [
                        'treeId' => $this->cat->tree_id,
                        'catId'  => $this->cat->id
                    ]);
                }
            }
        }
    }
}
