<?php namespace ss\stockPhotoRequest\commander\panel\controllers;

class Main extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

//    private $sPanel;

    private $s;

    private $d;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

//            $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);

            $this->s = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id, [
                'page'           => 1,
                'per_page'       => 50,
                'filters'        => [
                    'status'  => [
                        'pending' => true,
                        'done'    => true,
                    ],
                    'user_id' => false
                ],
                'focus'          => false,
                'target_user_id' => false
            ]);

            if (!$this->s['target_user_id']) {
                $this->s['target_user_id'] = 1; // todo первый из добавленных к ветке
            }

            $this->d = &$this->d('|tree-' . $this->tree->id, [
                'cache' => [
                    'requests_count_by_status' => [
                        'pending' => 0,
                        'done'    => 0
                    ]
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

    private function getRequestsBuilder()
    {
        $builder = \ss\stockPhotoRequest\models\Request::where('tree_id', $this->tree->id);//->where('from_user_id', $this->_user('id'));

        $statusFilter = ap($this->s, 'filters/status');

        $builder->where(function ($query) use ($statusFilter) {
            if ($statusFilter['done']) {
                $query->where('response_datetime', '>', 0);

                if ($statusFilter['pending']) {
                    $query->orWhere('request_datetime', '>', 0);
                }
            } else {
                if ($statusFilter['pending']) {
                    $query->where('response_datetime', '=', 0)->where('request_datetime', '>', 0);
                } else {
                    $query->where('response_datetime', '=', 0)->where('request_datetime', '=', 0);
                }
            }
        });

        if ($userFilter = ap($this->s, 'filters/user_id')) {
            $builder = $builder->where('to_user_id', $userFilter);
        }

        return $builder;
    }

    public function view()
    {
        $v = $this->v('|');

        $s = &$this->s;

        ///
        $builder = $this->getRequestsBuilder();

        $requestsCount = $builder->count();

        if ($requestsCount <= ($s['page'] - 1) * $s['per_page']) {
            $s['page'] = floor($requestsCount / $s['per_page']);
        }

        $requests = $builder->with(['product', 'toUser'])->orderBy('id')
            ->offset(($s['page'] - 1) * $s['per_page'])
            ->take($s['per_page'])
            ->get();
        ///

        $statuses = (new \ss\stockPhotoRequest\Main)->statuses;

        $n = 0;
        foreach ($requests as $request) {
            $n++;

            if ($request->response_datetime > 0) {
                $status = 'done';

                $date = \Carbon\Carbon::parse($request->response_datetime)->format('d.m.Y H:i:s');
            } else {
                $status = 'pending';

                $date = \Carbon\Carbon::parse($request->request_datetime)->format('d.m.Y H:i:s');
            }

            $v->assign('request', [
                'N'            => $n,
                'ID'           => $request->id,
                'PRODUCT_ID'   => $request->product_id,
                'DATE'         => $date,
                'NAME'         => $request->product->name,
                'USER'         => $request->toUser->login ?? '-',
                'STATUS_TITLE' => $status == 'done' ? 'Была сделана фотография' : 'Ожидание',
                'ICON_CLASS'   => $statuses[$status]['icon'],
                'STATUS_CLASS' => $status
            ]);
        }

        $this->assignFilter($v);

        $v->assign([
                       'FOCUS_CLASS'          => $this->panel->hasFocus('plugins') ? 'focus' : '',
                       'ADD_USER_SELECT'      => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:selectUser|',
                           'items'    => table_cells_by_id(\ss\models\User::all(), 'login'), // todo users
                           'selected' => $s['target_user_id']
                       ]),
                       'NOTIFY_DIALOG_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:notifyDialog',
                           'data'  => [
                               'tree' => xpack_model($this->tree)
                           ],
                           'class' => 'notify_dialog_button',
                           'icon'  => 'fa fa-envelope',
                           'title' => 'Отправить уведомления'
                       ]),
                       'PAGINATOR'            => $this->c('\std\ui paginator:view', [
                           'items_count' => $requestsCount,
                           'per_page'    => $s['per_page'],
                           'page'        => $s['page'],
                           'range'       => 2,
                           'controls'    => [
                               'page'          => [
                                   '\std\ui button:view',
                                   [
                                       'path'    => $this->_p('>xhr:setPage:%page|'),
                                       'class'   => 'page_button',
                                       'content' => '%page'
                                   ]
                               ],
                               'current_page'  => [
                                   '\std\ui button:view',
                                   [
                                       'class'   => 'page_button selected',
                                       'content' => '%page'
                                   ]
                               ],
                               'skipped_pages' => [
                                   '\std\ui button:view',
                                   [
                                       'class'   => 'skipped_pages_button',
                                       'content' => '...'
                                   ]
                               ]
                           ]
                       ])
                   ]);

        $this->css(':\css\std~');

        $this->widget(':|', [
            '.w'        => [
                'main'    => $this->_w('\ss\commander\ui~:|' . $this->panel->commander->instance),
                'panel'   => $this->_w('\ss\commander\ui\panel~:|' . $this->panel->instance),
                'content' => $this->_w('\ss\commander\ui\panel~content/' . $this->tree->mode . ':|' . $this->panel->instance)
            ],
            '.e'        => [
//                'ss/product/any/update_status' => 'mr.reload'
            ],
            '.r'        => [
                'reload' => $this->_p('>xhr:reload|'),
                'focus'  => $this->_p('>xhr:focus|'),
                'select' => $this->_p('>xhr:select|'),
                'open'   => $this->_p('>xhr:open|'),
                'add'    => $this->_p('>xhr:add|'),
            ],
            'focus'     => $s['focus'],
            //            'selection' => $s['selection'],
            'panelName' => $this->panel->name,
            'treeId'    => $this->tree->id
        ]);

        return $v;
    }

    public function assignFilter(\ewma\Views\View $v)
    {
        $statusFilter = ap($this->s, 'filters/status');
        $productsCountByStatus = ap($this->d, 'cache/requests_count_by_status');

        foreach ((new \ss\stockPhotoRequest\Main)->statuses as $status => $data) {
            $v->assign('status_filter', [
                'BUTTON' => $this->c('\std\ui button:view', [
                    'path'  => '>xhr:toggleStatusFilter|',
                    'data'  => [
                        'status' => $status
                    ],
                    'class' => 'button ' . $status . ($statusFilter[$status] ? ' enabled' : ''),
                    'icon'  => 'fa ' . $data['icon'],
                    'label' => $productsCountByStatus[$status] . ' '
                ])
            ]);
        }

        $v->assign([
                       'FILTER_USER_SELECT' => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:setFilterUser|',
                           'items'    => [0 => 'Все'] + table_cells_by_id(\ss\models\User::all(), 'login'), // todo users
                           'selected' => ap($this->s, 'filters/user_id')
                       ])
                   ]);

        return $v;
    }
}
