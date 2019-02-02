<?php namespace ss\cats\cp\pageNode\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('<:reload', [
                'cat' => $cat
            ]);
        }
    }

    public function create()
    {
        if ($cat = $this->unxpackModel('cat')) {
            if (ss()->cats->isCDable($cat)) {
                ss()->cats->createContainer($cat);

//                pusher()->trigger('ss/page/update_containers', [
//                    'id' => $cat->id
//                ]);

                pusher()->trigger('ss/cat/update_cats', [
                    'id' => $cat->id
                ]);

                pusher()->trigger('ss/tree/update_cats', [
                    'id' => $cat->tree_id
                ]);
            }
        }
    }

    public function arrange()
    {
        if ($cat = $this->unxpackModel('cat') and $this->dataHas('sequence array')) {
            if (ss()->cats->isEditable($cat)) {
                foreach ($this->data['sequence'] as $n => $nodeId) {
                    if (is_numeric($n) && $node = \ss\models\Cat::where('parent_id', $cat->id)->find($nodeId)) {
                        $node->update(['position' => ($n + 1) * 10]);
                    }
                }

                pusher()->trigger('ss/cat/update_cats', [
                    'id' => $cat->id
                ]);
            }
        }
    }
}
