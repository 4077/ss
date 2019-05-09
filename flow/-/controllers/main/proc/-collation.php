<?php namespace ss\flow\controllers\main\proc;

class _Collation extends \Controller
{
    /**
     * @var \ewma\Process\AppProcess
     */
    private $process;

    private $channel;

    private $settings;

    private $preprocessors;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->process = process();

            $this->settings = _j($this->channel->settings);
        } else {
            $this->lock();
        }
    }

    public function run()
    {
        $process = $this->process;
        $channel = $this->channel;

        $this->log('START COLLATION channel=' . $channel->id);

        $mode = ap($this->settings, 'collation/mode');

        $this->preprocessors['source'] = $this->getFieldPreprocessorController('source');
        $this->preprocessors['target'] = $this->getFieldPreprocessorController('target');

        #1
        $idsBySourceValues = $this->getIdsByFieldValues('source');
        $idsByTargetValues = $this->getIdsByFieldValues('target');

        #2
        $this->saveFoundIds();

        $sourceValues = array_keys($idsBySourceValues);
        $targetValues = array_keys($idsByTargetValues);

        \ss\flow\models\ProductsConnection::where('channel_id', $channel->id)->delete();

        if ($mode == 'equal') {
            $values = array_intersect($sourceValues, $targetValues);

            $count = count($values);
            $n = 0;

            $connectionsCount = 0;

            foreach ($values as $value) {
                if (true === $process->handleIteration(10)) {
                    break;
                }

                $n++;

                $sources = $idsBySourceValues[$value];
                $targets = $idsByTargetValues[$value];

                foreach ($sources as $sourceId) {
                    foreach ($targets as $targetId) {
                        $connection = \DB::table('ss_flow_products_connections')
                            ->where('channel_id', $channel->id)
                            ->where('source_id', $sourceId)
                            ->where('target_id', $targetId)
                            ->first();

                        if (!$connection) {
                            \ss\flow\models\ProductsConnection::create([
                                                                           'channel_id' => $channel->id,
                                                                           'source_id'  => $sourceId,
                                                                           'target_id'  => $targetId
                                                                       ]);
                        }

                        $connectionsCount++;
                    }
                }

                $process->output([
                                     'connections_count' => $connectionsCount
                                 ]);

                $process->progress($n, $count);

                $this->log($n . '/' . $count . ' [' . $connectionsCount . '] ' . $value);
            }
        }

        if ($mode == 'like_left' || $mode == 'like_right') {
            if ($mode == 'like_left') {
                $masterValues = $targetValues;
                $slaveValues = $sourceValues;
                $idsByMasterValues = $idsByTargetValues;
                $idsBySlaveValues = $idsBySourceValues;
            } else {
                $masterValues = $sourceValues;
                $slaveValues = $targetValues;
                $idsByMasterValues = $idsBySourceValues;
                $idsBySlaveValues = $idsByTargetValues;
            }

            $count = count($masterValues);
            $n = 0;

            $connectionsCount = 0;

            foreach ($masterValues as $masterValue) {
                if (true === $process->handleIteration(10)) {
                    break;
                }

                $n++;

                foreach ($slaveValues as $slaveValue) {
                    if (false !== mb_stripos($slaveValue, $masterValue)) {
                        $sources = $idsBySlaveValues[$slaveValue];
                        $targets = $idsByMasterValues[$masterValue];

                        foreach ($sources as $sourceId) {
                            foreach ($targets as $targetId) {
                                $connection = \DB::table('ss_flow_products_connections')
                                    ->where('channel_id', $channel->id)
                                    ->where('source_id', $sourceId)
                                    ->where('target_id', $targetId)
                                    ->first();

                                if (!$connection) {
                                    \ss\flow\models\ProductsConnection::create([
                                                                                   'channel_id' => $channel->id,
                                                                                   'source_id'  => $sourceId,
                                                                                   'target_id'  => $targetId
                                                                               ]);
                                }

                                $connectionsCount++;
                            }
                        }
                    }
                }

                $process->output(['connections_count' => $connectionsCount]);
                $process->progress($n, $count);

                $this->log($n . '/' . $count . ' [' . $connectionsCount . '] ' . $masterValue);
            }
        }

        $this->d(':xpids/collation', false, RR);

        pusher()->trigger('ss/flow/channel/collationComplete', [
            'channelId' => $channel->id
        ]);

        $this->log('COMPLETE COLLATION channel=' . $channel->id);
    }

    private $foundSourceIds = [];

    private $foundTargetIds = [];

    private function getIdsByFieldValues($type)
    {
        $field = ap($this->settings, 'collation/field/' . $type);

        $products = $this->getProductsField($type, $field);

        $output = [];

        foreach ($products as $product) {
            if ($fieldValue = $product->{$field}) {
                if ($this->preprocessors[$type]) {
                    $fieldValue = $this->preprocessors[$type]->perform($fieldValue);
                }

                if ($fieldValue) {
                    $output[$fieldValue][] = $product->id;

                    if ($type == 'source') {
                        $this->foundSourceIds[] = $product->id;
                    }

                    if ($type == 'target') {
                        $this->foundTargetIds[] = $product->id;
                    }
                }
            }
        }

        return $output;
    }

    private function saveFoundIds()
    {
        awrite($this->_protected('data', 'channel_' . $this->channel->id . '/sources.php'), $this->foundSourceIds);
        awrite($this->_protected('data', 'channel_' . $this->channel->id . '/targets.php'), $this->foundTargetIds);
    }

    private function getProductsField($type, $field)
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
