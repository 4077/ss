<?php namespace ss\cats\cp\container\common\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
//            $this->instance_($this->cat->id);
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
        $catXPack = xpack_model($cat);

        $fields = $this->c('~app:getFields');

        foreach ($fields as $field) {
            $v->assign(strtoupper($field), htmlspecialchars($cat->{$field}));
        }

        $toggleButtonsAccess = $this->a('ss:moderation');

        $v->assign([
                       'ENABLED_BUTTON'   => $this->c('\std\ui button:view', [
                           'visible' => $toggleButtonsAccess,
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button enabled ' . ($cat->enabled ? 'pressed' : ''),
                           'content' => $cat->enabled ? 'включен' : 'выключен'
                       ]),
                       'PUBLISHED_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => $toggleButtonsAccess,
                           'path'    => '>xhr:togglePublished',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button published ' . ($cat->published ? 'pressed' : ''),
                           'content' => $cat->published ? 'опубликован' : 'не опубликован'
                       ]),
                       'OUTPUT_BUTTON'    => $this->c('\std\ui button:view', [
                           'visible' => $toggleButtonsAccess,
                           'path'    => '>xhr:toggleOutputEnabled',
                           'data'    => [
                               'cat' => $catXPack
                           ],
                           'class'   => 'button output_enabled ' . ($cat->output_enabled ? 'pressed' : ''),
                           'content' => $cat->output_enabled ? 'результат отображается' : 'результат поглощается'
                       ]),
                       'COMPONENTS'       => $this->c('<<common/components~:view|', [
                           'cat' => $cat
                       ])
                   ]);

        $pinnedComponents = ss()->cats->getPinnedComponentsPivots($cat);

        foreach ($pinnedComponents as $component) {
            $v->assign('component_cp', [
                'CONTENT' => ss()->cats->renderComponentPivot($component, 'cp')
            ]);
        }

        $this->css(':\css\std~, \css\std flex');

        $this->widget(':|', [
            '.r'    => [
                'updateField' => $this->_p('>xhr:updateField|')
            ],
            'catId' => $cat->id,
            'cat'   => $catXPack
        ]);

        return $v;
    }
}
