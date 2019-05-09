<?php namespace ss\flow\controllers\main\proc;

class Update extends \Controller
{
    /**
     * @var \ewma\Process\AppProcess
     */
    private $process;

    private $channel;

    private $settings;

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

        $process->output(['status' => 'Запуск...']);

        $this->log('START UPDATE channel=' . $channel->id);

        $channel = $this->channel;

        $useTargetConnections = false;
        if (ap($this->settings, 'use_target_connections')) {
            $targetTree = $channel->target->tree;

            $ascendants = ss()->trees->connections->getAscendants($targetTree, '');
            $descendants = ss()->trees->connections->getDescendants($targetTree, '');

            if (count($ascendants) || count($descendants)) {
                $useTargetConnections = true;
            }
        }

        $posthandler = $this->getPosthandlerController();

        $connections = $channel->productsConnections;

        $allIds = [];
        $targetsIdsBySourcesIds = [];

        foreach ($connections as $connection) {
            $targetsIdsBySourcesIds[$connection->source_id][] = $connection->target_id;

            $allIds[] = $connection->source_id;
            $allIds[] = $connection->target_id;
        }

        $allIds = array_unique($allIds);

        $streams = $this->renderStreams();

        $products = table_rows_by_id(\ss\models\Product::whereIn('id', $allIds)->get());

        $count = count($targetsIdsBySourcesIds);
        $n = 0;

        foreach ($targetsIdsBySourcesIds as $sourceId => $targetsIds) {
            if (true === $process->handleIteration(10)) {
                break;
            }

            $n++;

            $source = $products[$sourceId];

            $multisourceCache = _j($source->multisource_cache);

            foreach ($targetsIds as $targetId) {
                $target = $products[$targetId];

                foreach ($streams as $stream) {
                    if ($stream['type'] == 'divisions') {
                        if ($divisionCache = $multisourceCache[$stream['source']] ?? false) {
                            $updateData = [
                                'division_id' => $stream['target']
                            ];

                            if ($stream['price'] && isset($divisionCache['price'])) {
                                $updateData['price'] = $divisionCache['price'] * $stream['price']['coefficient'];
                            }

                            if ($stream['discount'] && isset($divisionCache['discount'])) {
                                $updateData['discount'] = $divisionCache['discount'];
                            }

                            ss()->products->updateMultisourceData($target, $updateData, '', $useTargetConnections);
                        }
                    }

                    if ($stream['type'] == 'warehouses') {
                        if ($warehouseCache = ap($multisourceCache, $stream['source_division'] . '/warehouses/' . $stream['source'])) {
                            $updateData = [
                                'warehouse_id' => $stream['target']
                            ];

                            if ($stream['stock'] && isset($warehouseCache['stock'])) {
                                $updateData['stock'] = $warehouseCache['stock'];
                            }

                            if ($stream['reserved'] && isset($warehouseCache['reserved'])) {
                                $updateData['reserved'] = $warehouseCache['reserved'];
                            }

                            ss()->products->updateMultisourceData($target, $updateData, '', $useTargetConnections);
                        }
                    }
                }

                if ($posthandler) {
                    $posthandler->handle($source, $target);
                }

                $process->output(['status' => '']);
                $process->progress($n, $count);

                $this->log($n . '/' . $count . ' source=' . $sourceId . ' target=' . $targetId);
            }
        }

        $this->log('COMPLETE UPDATE channel=' . $channel->id);
    }

    private function renderStreams()
    {
        $settings = _j($this->channel->settings);

        $output = [];

        $streams = ap($settings, 'streams') ?? [];

        foreach ($streams as $stream) {
            if ($stream['enabled'] && $stream['source'] && $stream['target']) {
                $data = [
                    'type'     => 'divisions',
                    'source'   => $stream['source'],
                    'target'   => $stream['target'],
                    'discount' => $stream['data']['discount']['enabled'],
                    'price'    => false
                ];

                if ($stream['data']['price']['enabled']) {
                    if ($stream['data']['price']['use_coefficients_table']) {
                        if ($intersection = ss()->multisource->divisionsIntersections->getIntersectionByIds($stream['source'], $stream['target'])) {
                            $coefficient = $intersection->price_coefficient;
                        } else {
                            $coefficient = 1;
                        }
                    } else {
                        $coefficient = $stream['data']['price']['coefficient'];
                    }

                    $data['price'] = [
                        'coefficient' => $coefficient
                    ];
                }

                if ($data['price'] || $data['discount']) {
                    $output[] = $data;
                }

                foreach ($stream['warehouses'] as $warehousesStream) {
                    $data = [
                        'type'            => 'warehouses',
                        'source_division' => $stream['source'],
                        'source'          => $warehousesStream['id'],
                        'target'          => $warehousesStream['id'],
                        'stock'           => $warehousesStream['data']['stock']['enabled'],
                        'reserved'        => $warehousesStream['data']['reserved']['enabled']
                    ];

                    if ($data['stock'] || $data['target']) {
                        $output[] = $data;
                    }
                }
            }
        }

        return $output;
    }

    private function getPosthandlerController()
    {
        if ($enabled = ap($this->settings, 'posthandler_enabled')) {
            $controller = $this->c('\customNodes\ss\flow\posthandlers channel_' . $this->channel->id);

            if ($controller->__meta__->virtual) {
                $this->log('ERROR: posthandler controller for channel=' . $this->channel->id . ' does not exists');

                $this->process->terminate();

                exit;
            }
        } else {
            $controller = false;
        }

        return $controller;
    }
}
