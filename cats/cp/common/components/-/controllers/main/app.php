<?php namespace ss\cats\cp\common\components\controllers\main;

class App extends \Controller
{
    public function componentSelect()
    {
        $cat = $this->unpackModel('cat');
        $component = $this->data('component');

        if ($cat && $component) {
            $data = [];
            if ($defaultDataHandler = components()->getHandler($component, 'default-data')) {
                $data = handlers()->render($defaultDataHandler);
            }

            \ss\models\CatComponent::create([
                                                'cat_id'       => $cat->id,
                                                'component_id' => $component->id,
                                                'type'         => $this->data('type'),
                                                'data'         => j_($data)
                                            ]);

            pusher()->trigger('ss/cat/components_update.' . $cat->id);
        }
    }

    public function readPivotData()
    {
        if ($pivot = $this->unpackModel('pivot')) {
            return _j($pivot->data);
        }
    }

    public function writePivotData()
    {
        if ($pivot = $this->unpackModel('pivot')) {
            $pivot->data = j_($this->data('data'));
            $pivot->save();
        }
    }
}
