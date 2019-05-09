<?php namespace ss\flow\controllers\main\proc;

class Collation extends \Controller
{
    /**
     * @var \ewma\Process\AppProcess
     */
    private $process;

    /**
     * @var \ss\flow\models\Channel
     */
    private $channel;

    private $settings;

    private $preprocessors;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->process = process();

            $this->settings = _j($this->channel->settings);

            $this->preprocessors['source'] = $this->getFieldPreprocessorController('source');
            $this->preprocessors['target'] = $this->getFieldPreprocessorController('target');
        } else {
            $this->lock();
        }
    }

    private function resetValues()
    {
        $this->channel->collation()->delete();
    }

    private function collectValues()
    {
        $this->collectEndpointValues('source');
        $this->collectEndpointValues('target');
    }

    private function collectEndpointValues($type)
    {
        $process = $this->process;

        $field = ap($this->settings, 'collation/field/' . $type);

        $products = $this->getProductsFieldValues($type, $field);

        $count = count($products);
        $n = 0;

        $output = [];

        $builder = \DB::table('ss_flow_collation');

        $preprocessor = $this->preprocessors[$type];

        foreach ($products as $product) {
            if (true === $process->handleIteration()) {
                break;
            }

            $n++;

            $productId = $product->id;

            if ($originFieldValue = $product->{$field}) {
                if ($preprocessor) {
                    $fieldValue = $preprocessor->perform($originFieldValue);
                    $logSuffix = ' < ' . $originFieldValue;
                } else {
                    $fieldValue = $originFieldValue;
                    $logSuffix = '';
                }

                if ($fieldValue) {
                    $collationData = [
                        'channel_id' => $this->channel->id,
                        'product_id' => $product->id,
                        'type'       => $type,
                        'value'      => $fieldValue
                    ];

                    $builder->insert($collationData);

                    $this->log($n . '/' . $count . ' ' . $type . ' product ' . $productId . ' INSERT ' . $fieldValue . $logSuffix);
                } else {
                    $this->log($n . '/' . $count . ' ' . $type . ' product ' . $productId . '   SKIP ' . $type . $logSuffix);
                }
            } else {
                $this->log($n . '/' . $count . ' ' . $type . ' product ' . $productId . '  EMPTY');
            }

            $process->progress($n, $count);
        }

        return $output;
    }

    private function getProductsFieldValues($type, $field)
    {
        $tree = $this->channel->{$type}->tree;

        if ($typeCatsIds = ap($this->settings, 'cats/' . $type)) {
            $cats = \ss\models\Cat::whereIn('id', $typeCatsIds)->get();

            $catsIds = [];

            foreach ($cats as $cat) {
                $catSubtreeIds = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $tree->id))->getIds($cat);

                merge($catsIds, $catSubtreeIds);
            }

            $products = \DB::table('ss_products')->select('id', $field)->whereIn('cat_id', $catsIds)->get();
        } else {
            $products = \DB::table('ss_products')->select('id', $field)->where('tree_id', $tree->id)->get();
        }

        return $products;
    }

    private function collate()
    {
        \ss\flow\models\ProductsConnection::where('channel_id', $this->channel->id)->delete();

        $mode = ap($this->settings, 'collation/mode');

        if ($mode == 'equal') {
            $this->collateEqual();
        }

        if ($mode == 'like_left') {
            $this->collateLike('target');
        }

        if ($mode == 'like_right') {
            $this->collateLike('source');
        }
    }

    private function collateLike($master)
    {
        $process = $this->process;

        $slave = $master == 'target' ? 'source' : 'target';

        $rows = \DB::table('ss_flow_collation')
            ->where('channel_id', $this->channel->id)
            ->where('type', $master)
            ->get();

        $hasConnectionsCount = 0;
        $connectionsCount = 0;

        $count = count($rows);
        $n = 0;

        foreach ($rows as $row) {
            if (true === $process->handleIteration()) {
                break;
            }

            $n++;

            $masterProductId = $row->product_id;

            $this->log($n . '/' . $count . ' [' . $hasConnectionsCount . ' < ' . $connectionsCount . '] ' . $masterProductId . ' ' . $row->value);

            $connections = \DB::table('ss_flow_collation')
                ->where('channel_id', $this->channel->id)
                ->where('value', 'like', '%' . $row->value . '%')
                ->where('type', $slave)
                ->get();

            if (count($connections)) {
                $hasConnectionsCount++;
            }

            foreach ($connections as $connection) {
                $connectionsCount++;

                $slaveProductId = $connection->product_id;

                \ss\flow\models\ProductsConnection::create([
                                                               'channel_id'    => $this->channel->id,
                                                               $master . '_id' => $masterProductId,
                                                               $slave . '_id'  => $slaveProductId
                                                           ]);

                $this->log($n . '/' . $count . ' [' . $hasConnectionsCount . ' < ' . $connectionsCount . ']  └ ' . $connection->id);
            }

            $process->output(['connections_count' => $connectionsCount]);
            $process->progress($n, $count);
        }
    }

    private function collateEqual()
    {
        $process = $this->process;

        $sourcesCount = \DB::table('ss_flow_collation')->where('channel_id', $this->channel->id)->where('type', 'source')->count();
        $targetsCount = \DB::table('ss_flow_collation')->where('channel_id', $this->channel->id)->where('type', 'target')->count();

        if ($sourcesCount > $targetsCount) {
            $master = 'target';
            $slave = 'source';
        } else {
            $master = 'source';
            $slave = 'target';
        }

        $rows = \DB::table('ss_flow_collation')
            ->where('channel_id', $this->channel->id)
            ->where('type', $master)
            ->get();

        $hasConnectionsCount = 0;
        $connectionsCount = 0;

        $count = count($rows);
        $n = 0;

        foreach ($rows as $row) {
            if (true === $process->handleIteration()) {
                break;
            }

            $n++;

            $masterProductId = $row->product_id;

            $this->log($n . '/' . $count . ' [' . $hasConnectionsCount . ' < ' . $connectionsCount . '] ' . $masterProductId . ' ' . $row->value);

            $connections = \DB::table('ss_flow_collation')
                ->where('channel_id', $this->channel->id)
                ->where('value', $row->value)
                ->where('type', $slave)
                ->get();

            if (count($connections)) {
                $hasConnectionsCount++;
            }

            foreach ($connections as $connection) {
                $connectionsCount++;

                $slaveProductId = $connection->product_id;

                \ss\flow\models\ProductsConnection::create([
                                                               'channel_id'    => $this->channel->id,
                                                               $master . '_id' => $masterProductId,
                                                               $slave . '_id'  => $slaveProductId
                                                           ]);

                $this->log($n . '/' . $count . ' [' . $hasConnectionsCount . ' < ' . $connectionsCount . ']  └ ' . $connection->id);
            }

            $process->output(['connections_count' => $connectionsCount]);
            $process->progress($n, $count);
        }
    }

    public function run()
    {
        $process = $this->process;
        $channel = $this->channel;

        $this->log('START COLLATION channel=' . $channel->id);

        $this->resetValues();
        $this->collectValues();
        $this->collate();

        $this->d(':xpids/collation', false, RR);

        pusher()->trigger('ss/flow/channel/collationComplete', [
            'channelId' => $channel->id
        ]);

        $this->log('COMPLETE COLLATION channel=' . $channel->id);
    }

    private function getFieldPreprocessorController($type)
    {
        if ($enabled = ap($this->settings, 'collation/field_preprocessor_enabled/' . $type)) {
            $controller = $this->c('\customNodes\ss\flow\fieldsPreprocessors channel_' . $this->channel->id . '/' . $type);

            if ($controller->__meta__->virtual) {
                $this->log('ERROR: preprocessor controller for channel=' . $this->channel->id . ' and type=' . $type . ' does not exists');

                $this->process->terminate();

                exit;
            }
        } else {
            $controller = false;
        }

        return $controller;
    }
}
