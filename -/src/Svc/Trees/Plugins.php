<?php namespace ss\Svc\Trees;

class Plugins extends \ewma\Service\Service
{
    public function get(\ss\models\Tree $tree)
    {
        $treeData = _j($tree->data);

        return (array)ap($treeData, 'plugins');
    }

    public function getEnabled(\ss\models\Tree $tree)
    {
        $plugins = $this->get($tree);

        $enabledPlugins = [];

        foreach ($plugins as $name => $data) {
            if ($data['enabled']) {
                $enabledPlugins[$name] = $data;
            }
        }

        return $enabledPlugins;
    }

    public function pluginData(\ss\models\Tree $tree, $plugin, $path = false, $value = null)
    {
        $treeData = _j($tree->data);

        if (null === $value) {
//            return (array)ap($treeData, path('plugins', $plugin, $path));
            return ap($treeData, path('plugins', $plugin, $path));
        } else {
            ap($treeData, path('plugins', $plugin, $path), $value);

            $tree->data = j_($treeData);
            $tree->save();
        }
    }
}
