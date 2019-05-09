<?php namespace ss\flow\ui\channel\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('~:reload', [], 'channel');
    }

    private function settings($channel, $path, $value = null)
    {
        $settings = _j($channel->settings);

        if (null === $value) {
            return ap($settings, $path);
        } else {
            ap($settings, $path, $value);

            $channel->settings = j_($settings);
            $channel->save();
        }
    }

    public function selectCollationField()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->settings($channel, 'collation/field/' . $this->data('type'), $this->data('value'));
        }
    }

    public function selectCollationMode()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->settings($channel, 'collation/mode', $this->data('value'));
        }
    }

    public function toggleConnectionsUsing()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $enabled = $this->settings($channel, 'use_target_connections');

            $this->settings($channel, 'use_target_connections', !$enabled);

            $this->reload();
        }
    }

    public function addFilteringCat()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->c('\std\ui\dialogs~:open:catSelector, ss|ss/flow/channels', [
                'path'          => 'controls/catSelector~:view',
                'data'          => [
                    'channel' => pack_model($channel),
                    'type'    => $this->data('type')
                ],
                'pluginOptions' => [
                    'title' => ''
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 350,
                        'height' => 400
                    ]
                ]
            ]);
        }
    }

    public function removeFilteringCat()
    {
        $channel = $this->unxpackModel('channel');
        $cat = $this->unxpackModel('cat');

        if ($channel && $cat) {
            $type = $this->data('type');

            $cats = $this->settings($channel, 'cats/' . $type);

            diff($cats, $cat->id);

            $this->settings($channel, 'cats/' . $type, $cats);

            $this->reload();

            pusher()->trigger('ss/flow/channel/settings/updateFilteringCats', [
                'channelId' => $channel->id
            ]);
        }
    }

    public function toggleFieldPreprocessor()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $enabled = $this->settings($channel, 'collation/field_preprocessor_enabled/' . $this->data('type'));

            invert($enabled);

            $this->settings($channel, 'collation/field_preprocessor_enabled/' . $this->data('type'), $enabled);

            $this->reload();
        }
    }

    public function editFieldPreprocessor()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->c('\std\ui\dialogs~:open:fieldPreprocessor, ss|ss/flow/channels', [
                'path'          => 'controls/fieldPreprocessor~:view',
                'data'          => [
                    'channel' => pack_model($channel),
                    'type'    => $this->data('type')
                ],
                'pluginOptions' => [
                    'title' => ''
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 600,
                        'height' => 400
                    ]
                ]
            ]);
        }
    }

    public function selectTransferCpTab()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->s('~:update_cp/selected_tab', $this->data('tab'), RR);

            $this->reload();
        }
    }

    public function togglePosthandler()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $enabled = $this->settings($channel, 'posthandler_enabled');

            invert($enabled);

            $this->settings($channel, 'posthandler_enabled', $enabled);

            $this->reload();
        }
    }

    public function editPosthandler()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->c('\std\ui\dialogs~:open:posthandler, ss|ss/flow/channels', [
                'path'          => 'controls/posthandler~:view',
                'data'          => [
                    'channel' => pack_model($channel),
                    'type'    => $this->data('type')
                ],
                'pluginOptions' => [
                    'title' => ''
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 600,
                        'height' => 400
                    ]
                ]
            ]);
        }
    }

    public function collate()
    {
        if ($channel = $this->unxpackModel('channel')) {
//            if ($channel = $this->unxpackModel('channel')) {
//                $process = $this->c('^:collate', [
//                    'channel' => $channel
//                ]);
//
//                if ($process) {
//                    $this->app->response->json(['xpid' => $process->getXPid()]);
//                }
//            }

            if ($this->data('sync')) {
                $this->c('^~proc/collation:run', [
                    'channel' => pack_model($channel)
                ]);
            } else {
                $process = $this->proc('^~proc/collation:run', [
                    'channel' => pack_model($channel)
                ])->pathLock()->run();

                if ($process) {
                    $this->d('^~:xpids/collation', $process->getPid(), RR);

                    pusher()->trigger('ss/flow/channel/collationStart', [
                        'xpid' => $process->getXPid()
                    ]);

                    $this->app->response->json(['xpid' => $process->getXPid()]);
                }
            }
        }
    }

    public function update()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $process = $this->c('^:update', [
                'channel' => $channel
            ]);

            if ($process) {
                $this->app->response->json(['xpid' => $process->getXPid()]);
            }
        }
    }

    public function collationInfo()
    {
        if ($channel = $this->unxpackModel('channel')) {
            $this->c('\std\ui\dialogs~:open:collationInfo, ss|ss/flow/channels', [
                'path'          => 'collationInfo~:view',
                'data'          => [
                    'channel' => pack_model($channel)
                ],
                'pluginOptions' => [
                    'title' => $channel->id
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 800,
                        'height' => 600
                    ]
                ]
            ]);
        }
    }
}
