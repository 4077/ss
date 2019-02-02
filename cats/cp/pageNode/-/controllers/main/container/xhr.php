<?php namespace ss\cats\cp\pageNode\controllers\main\container;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reloadContainerUi()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\ss\cats\ui~container:reload', [
                'cat' => $cat
            ]);
        }
    }

    public function containerDialog()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('\ss\cats\cp dialogs:container|ss/cats', [
                'cat' => $cat
            ]);
        }
    }

    public function delete()
    {
        if ($this->data('discarded')) {
            $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/cats');
        } else {
            if ($cat = $this->unxpackModel('cat')) {
                if (ss()->cats->isCDable($cat)) {
                    if ($this->data('confirmed')) {
                        $cat->delete();

                        pusher()->trigger('ss/page/update_containers', [
                            'id' => $cat->parent_id
                        ]);

                        pusher()->trigger('ss/tree/' . $cat->parent->tree_id . '/update_containers'); // ?

                        $this->c('\std\ui\dialogs~:close:deleteConfirm|ss/cats');
                    } else {
                        $this->c('\std\ui\dialogs~:open:deleteConfirm|ss/cats', [
                            'path'            => '\std dialogs/confirm~:view',
                            'data'            => [
                                'confirm_call' => $this->_abs(':delete', ['cat' => $this->data['cat']]),
                                'discard_call' => $this->_abs(':delete', ['cat' => $this->data['cat']]),
                                'message'      => 'Удалить контейнер <b>' . (ss()->cats->getName($cat) ?: '...') . '</b>?'
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
    }
}
