<?php namespace ss\cats\cp\common\components\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload|', [], true);
    }

    public function componentSelectorDialog()
    {
        if ($cat = $this->unpackModel('cat')) {
            $availableComponentsCatsIds = ss()->trees->getAvailableComponentsCatsIds($cat->tree_id, $cat->type, $this->data('type'));

            $this->c('\std\ui\dialogs~:open:componentSelector, ss|', [
                'path' => '\ewma\components\selector~:view|ss/tree/' . $cat->tree_id,
                'data' => [
                    'available_cats_ids'    => $availableComponentsCatsIds,
                    'selected_component_id' => $cat->component->id ?? false,
                    'callbacks'             => [
                        'select' => $this->_abs('~app:componentSelect', [
                            'cat'       => pack_model($cat),
                            'type'      => $this->data('type'),
                            'component' => '%component'
                        ])
                    ]
                ]
            ]);
        }
    }

    public function toggle()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $pivot->enabled = !$pivot->enabled;
            $pivot->save();

            pusher()->trigger('ss/cat/components_update.' . $pivot->cat_id);
        }
    }

    public function togglePinned()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $pivot->pinned = !$pivot->pinned;
            $pivot->save();

            pusher()->trigger('ss/cat/components_update.' . $pivot->cat_id);
        }
    }

    public function componentDialog()
    {
        if ($pivot = $this->unxpackModel('pivot') and $instance = _j64($this->data('instance'))) {
            $cat = $pivot->cat;

            $titleCallPath = false;

            if ($cat->type == 'page') {
                $titleCallPath = '\ss\cats\cp\page~dialogTitle:view|';
            }

            if ($cat->type == 'container') {
                $titleCallPath = '\ss\cats\cp\container~dialogTitle:view|';
            }

            $this->c('\std\ui\dialogs~:open:component_' . $instance . '_' . $pivot->component_id . ', ss|', [
                'path'      => '@component~:view',
                'data'      => [
                    'pivot'    => pack_model($pivot),
                    'instance' => $instance
                ],
                'title'     => $titleCallPath
                    ? $this->_abs($titleCallPath, [
                        'cat' => pack_model($cat)
                    ])
                    : false,
                'class'     => '',
                'callbacks' => [
                    'update' => $this->_p('~app:dialogDataUpdate')
                ]
            ]);
        }
    }

    public function dataDialog()
    {
        if ($pivot = $this->unxpackModel('pivot')) {
            $callData = [
                'pivot' => pack_model($pivot)
            ];

            $this->c('\std\ui\dialogs~:open:pivotData, ss|', [
                'path'          => '\std\ui\data~:view|' . $this->_nodeId() . '/' . $pivot->id,
                'data'          => [
                    'read_call'  => $this->_abs('~app:readPivotData', $callData),
                    'write_call' => $this->_abs('~app:writePivotData', $callData)
                ],
                'pluginOptions' => [
                    'title' => 'pivot:' . $pivot->id . ' cat:' . $pivot->cat_id . ' component:' . $pivot->component_id
                ]
            ]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|');
        } else {
            if ($pivot = $this->unxpackModel('pivot')) {
                if ($this->data('confirmed')) {
                    $pivot->delete();

                    if ($component = $pivot->component) {
                        pusher()->trigger('ss/cat/components_update.' . $pivot->cat_id);
                    }

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|');
                } else {
                    if ($component = $pivot->component) {
                        $componentName = components()->getFullName($component);
                    } else {
                        $componentName = '...';
                    }

                    $this->c('\std\ui\dialogs~:open:deleteConfirm|', [
                        'path'            => '\std dialogs/confirm~:view',
                        'data'            => [
                            'confirm_call' => $this->_abs(':delete', ['pivot' => $this->data['pivot']]),
                            'discard_call' => $this->_abs(':delete', ['pivot' => $this->data['pivot']]),
                            'message'      => 'Удалить компонент <b>' . $componentName . '</b>?'
                        ],
                        'forgot_on_close' => true,
                        'pluginOptions'   => [
                            'resizable' => 'false'
                        ]
                    ]);
                }
            }
        }
    }

    public function arrange()
    {
        if ($cat = $this->unxpackModel('cat') and $this->dataHas('sequence array')) {
            foreach ($this->data['sequence'] as $n => $nodeId) {
                if (is_numeric($n) && $node = \ss\models\CatComponent::where('cat_id', $cat->id)->find($nodeId)) {
                    $node->update(['position' => ($n + 1) * 10]);
                }
            }

            pusher()->triggerOthers('ss/cat/components_update' . $cat->id);
        }
    }
}
