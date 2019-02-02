<?php namespace ss\cp\trees\tree\plugins\controllers;

class Main extends \Controller
{
    private $s;

    private $tree;

    public function __create()
    {
        if ($this->tree = $this->unpackModel('tree')) {
            $this->s = &$this->s(false, [
                'selected_plugin_name' => false
            ]);
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
        $s = $this->s;

        $tree = $this->tree;
        $treeXPack = xpack_model($tree);

        // todo optimize plugins
        $pluginsComponentsCatId = ss()->config('trees/plugins/components_cat_id');

        $pluginsComponents = \ewma\components\models\Component::where('cat_id', $pluginsComponentsCatId)->orderBy('position')->get();

        foreach ($pluginsComponents as $pluginComponent) {
            $pluginName = $pluginComponent->name;

            if ($pluginDataHandler = components()->getHandler($pluginComponent, 'data')) {
                $pluginData = handlers()->render($pluginDataHandler);

                $selected = $s['selected_plugin_name'] == $pluginName;

                $enabled = ss()->trees->plugins->pluginData($tree, $pluginName, 'enabled');
                $treePluginData = ss()->trees->plugins->pluginData($tree, $pluginName, 'data');

                $v->assign('plugin', [
                    'ENABLED_CLASS'  => $enabled ? 'enabled' : '',
                    'SELECTED_CLASS' => $selected ? 'selected' : '',
                    'PLUGIN_NAME'    => $pluginName,
                    'NAME'           => $pluginData['name'] ?? '',
                    'TOGGLE_BUTTON'  => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:toggle',
                        'data'  => [
                            'tree' => $treeXPack,
                            'name' => $pluginName
                        ],
                        'class' => 'toggle button',
                        'icon'  => 'fa fa-power-off'
                    ])
                ]);

                $this->c('\std\ui button:bind', [
                    'selector' => $this->_selector('|') . " .plugin[name='" . $pluginName . "']",
                    'path'     => '>xhr:select',
                    'data'     => [
                        'tree' => $treeXPack,
                        'name' => $pluginName
                    ]
                ]);

                if ($selected) {
                    if ($pluginCpHandler = components()->getHandler($pluginComponent, 'cp')) {
                        $v->assign([
                                       'CP' => handlers()->render($pluginCpHandler, [
                                           'tree' => $this->tree
                                       ])
                                   ]);
                    }
                }
            }
        }

        $this->css();

        return $v;
    }
}
