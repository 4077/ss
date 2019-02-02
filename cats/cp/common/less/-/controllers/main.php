<?php namespace ss\cats\cp\common\less\controllers;

class Main extends \Controller
{
    private $s;

    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
            $this->instance_($this->cat->id);

            $this->s = &$this->s();
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

        $cat = $this->cat;
        $catPack = pack_model($cat);
        $catXPack = xpack_model($cat);

        $lessTypes = ss()->cats->getLessTypes($cat->type);

        $selectedLessType = &ap($this->s, 'selected_less_type_by_cat_type/' . $cat->type);
        if (!$selectedLessType) {
            $selectedLessType = current($lessTypes);
        }

        $modulePath = 'customNodes/ss/cats/less';

        if (!$this->app->modules->getByPath($modulePath)) {
            $this->c('\ewma\dev~:createModule', [
                'path'  => $modulePath,
                'reset' => true
            ]);

            $this->app->modules->reload();
        }

        $targetNode = $modulePath . ' cat_' . $cat->id . '/' . $selectedLessType;

        $v->assign([
                       'CP'     => $this->c('>cp:view', [
                           'cat' => $cat
                       ]),
                       'EDITOR' => $this->c('\ewma\nodeFileEditor~:view|ss/cats/' . $cat->id . '/less/' . $selectedLessType, [
                           'node_type'                  => 'less',
                           'target_node'                => $targetNode,
                           'template_node'              => $this->_p('data/codeTemplates/' . $selectedLessType),
                           'callbacks'                  => [
                               'save'   => $this->_abs('>app:onSave', ['cat' => $catPack]),
                               'reset'  => $this->_abs('>app:onReset', ['cat' => $catPack]),
                               'update' => $this->_abs('>app:onUpdate', ['cat' => $catPack])
                           ],
                           'resizable_closest_selector' => '.ui-dialog'
                       ])
                   ]);

        $this->css();

        $this->c('\css\fontawesome~:load');

        $this->widget(':|', [
            '.e' => [
                'ss/cat/' . $cat->id . '/less/update' => 'mr.reload',
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload', [
                    'cat' => $catXPack
                ])
            ]
        ]);

        return $v;
    }
}
