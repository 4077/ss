<?php namespace ss\flow\ui\channel\collationInfo\controllers;

class _Main extends \Controller
{
    private $channel;

    private $channelXPack;

    private $sourceIds = [];

    private $targetIds = [];

    private $products;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

            $this->sourceIds = aread($this->_protected('data', '^~proc/collation:channel_' . $this->channel->id . '/sources.php')) ?? [];
            $this->targetIds = aread($this->_protected('data', '^~proc/collation:channel_' . $this->channel->id . '/targets.php')) ?? [];

            $allIds = [];

            merge($allIds, $this->sourceIds);
            merge($allIds, $this->targetIds);

            $products = \DB::table('ss_products')->select(['id', 'name'])->whereIn('id', $allIds)->get();

            foreach ($products as $product) {
                $this->products[$product->id] = $product;
            }

            $this->instance_($this->channel->id);

            $this->s('|', [
                'scrolls' => [
                    'connections' => 0,
                    'source'      => 0,
                    'target'      => 0
                ]
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

        $connectionsCount = $this->assignConnections($v);
        $sourcesCount = $this->assignEndpointProducts($v, 'source');
        $targetsCount = $this->assignEndpointProducts($v, 'target');

        $v->assign([
                       'SOURCES_COUNT'     => $sourcesCount,
                       'TARGETS_COUNT'     => $targetsCount,
                       'CONNECTIONS_COUNT' => $connectionsCount,
                   ]);

        $this->css();

        $this->c('\js\jquery\mousewheel~:load');

        $this->widget(':|', [
            'scrolls' => $this->s(':scrolls|'),
            '.r'      => [
                'updateScroll'  => $this->_p('>xhr:updateScroll|'),
                'updateScrolls' => $this->_p('>xhr:updateScrolls|'),
                'openProduct'   => $this->_p('>xhr:openProduct')
            ]
        ]);

        return $v;
    }

    private function assignEndpointProducts(\ewma\Views\View $v, $type)
    {
        $ids = $this->{$type . 'Ids'};

        $n = 0;
        foreach ($ids as $id) {
            $v->assign($type, [
                'N'               => $n,
                'ID'              => $id,
                'FIELD_VALUE'     => $this->products[$id]->name,
                'CONTENT'         => $id,
                'CONNECTED_CLASS' => in_array($id, $this->connectedIds) ? 'connected' : ''
            ]);

            if (++$n > 250) {
//                break;
            }
        }

        return count($ids);
    }

    private $connectedIds = [];

    private function assignConnections(\ewma\Views\View $v)
    {
        $connections = $this->channel->productsConnections;

        $n = 0;
        foreach ($connections as $connection) {
            $v->assign('connection', [
                'N'         => $n,
                'SOURCE_ID' => $connection->source_id,
                'TARGET_ID' => $connection->target_id
            ]);

            $this->connectedIds[] = $connection->source_id;
            $this->connectedIds[] = $connection->target_id;

            if (++$n > 250) {
//                break;
            }
        }

        return count($connections);
    }
}
