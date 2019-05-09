<?php namespace ss\flow\ui\channel\controls\catSelector\controllers;

class Main extends \Controller
{
    private $channel;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->instance_($this->channel->id);
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

        $type = $this->data('type');

        $channel = $this->channel;

        $tree = $channel->{$type}->tree;

        $rootNode = ss()->trees->getRootCat($tree->id);

        $settings = _j($channel->settings);

        $selectedNodesIds = ap($settings, 'cats/' . $type);

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\tree~:view|' . $this->_nodeInstance() . '/' . $type, [
                           'default'           => [

                           ],
                           'node_control'      => [
                               '>node:view|',
                               [
                                   'root_node_id'      => $rootNode->id,
                                   'cat'               => '%model',
                                   'channel'           => pack_model($channel),
                                   'type'              => $type,
                                   'selected_cats_ids' => $selectedNodesIds
                               ]
                           ],
                           'query_builder'     => $this->_abs('>app:getQueryBuilder', [
                               'tree_id' => $tree->id
                           ]),
                           'root_node_id'      => $rootNode->id,
                           'expand'            => false,
                           'sortable'          => false,
                           'movable'           => false,
                           'root_node_visible' => false,
                           'filter_ids'        => false
                       ])
                   ]);

        $this->css();

        $this->widget(':|', [
            '.e' => [
                'ss/flow/channel/settings/updateFilteringCats' => 'r.reload'
            ],
            '.r' => [
                'reload' => $this->_abs('>xhr:reload', [
                    'channel' => xpack_model($channel),
                    'type'    => $this->data('type')
                ])
            ]
        ]);

        return $v;
    }
}
