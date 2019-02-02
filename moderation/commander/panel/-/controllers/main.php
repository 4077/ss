<?php namespace ss\moderation\commander\panel\controllers;

class Main extends \Controller
{
    /**
     * @var $panel \ss\commander\Svc\Panel
     */
    private $panel;

    private $tree;

    private $cat;

    private $sPanel;

    private $s;

    private $d;

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

            $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);

            $this->s = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id, [
                'page'     => 1,
                'per_page' => 50,
                'filters'  => [
                    'status' => [
                        'initial'    => true,
                        'temporary'  => true,
                        'scheduled'  => true,
                        'discarded'  => true,
                        'moderation' => true
                    ]
                ],
                'focus'    => false
            ]);

            $this->d = &$this->d('|tree-' . $this->tree->id, [
                'cache' => [
                    'products_count_by_status' => [
                        'initial'    => 0,
                        'temporary'  => 0,
                        'scheduled'  => 0,
                        'discarded'  => 0,
                        'moderation' => 0
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

    private function getProductsBuilder()
    {
        $builder = $this->tree->products();

        $enabledStatuses = [];

        $statusFilter = ap($this->s, 'filters/status');
        foreach ($statusFilter as $status => $enabled) {
            if ($enabled) {
                $enabledStatuses[] = $status;
            }
        }

        if ($enabledStatuses) {
            $builder = $builder->whereIn('status', $enabledStatuses);
        }

        return $builder;
    }

    public function view()
    {
        $v = $this->v('|');

        $s = &$this->s;

        ///
        $builder = $this->getProductsBuilder();

        $productsCount = $builder->count();

        if ($productsCount <= ($s['page'] - 1) * $s['per_page']) {
            $s['page'] = floor($productsCount / $s['per_page']);
        }

        $products = $builder->orderBy('status_datetime')
            ->offset(($s['page'] - 1) * $s['per_page'])
            ->take($s['per_page'])
            ->get();
        ///

        $statuses = (new \ss\moderation\Main)->statuses;

        $n = 0;
        foreach ($products as $product) {
            $n++;

            $v->assign('product', [
                'N'            => $n,
                'PRODUCT_ID'   => $product->id,
                'DATE'         => \Carbon\Carbon::parse($product->status_datetime)->format('d.m.Y H:i:s'),
                'NAME'         => $product->name,
                'STATUS_TITLE' => $statuses[$product->status]['title'] . ' (' . \Carbon\Carbon::parse($product->status_datetime)->format('d.m.Y H:i:s') . ')',
                'ICON_CLASS'   => $statuses[$product->status]['icon'],
                'STATUS_CLASS' => $product->status
            ]);
        }

        $v = $this->assignStatusFilter($v);

        $v->assign([
                       'FOCUS_CLASS' => $this->panel->hasFocus('plugins') ? 'focus' : '',
                       'PAGINATOR'   => $this->c('\std\ui paginator:view', [
                           'items_count' => $productsCount,
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

        $this->css();

        $this->widget(':|', [
            '.w'        => [
                'main'    => $this->_w('\ss\commander\ui~:|' . $this->panel->commander->instance),
                'panel'   => $this->_w('\ss\commander\ui\panel~:|' . $this->panel->instance),
                'content' => $this->_w('\ss\commander\ui\panel~content/' . $this->tree->mode . ':|' . $this->panel->instance)
            ],
            '.e'        => [
                'ss/product/any/update_status' => 'mr.reload'
            ],
            '.r'        => [
                'reload' => $this->_abs('>xhr:reload|'),
                'focus'  => $this->_p('>xhr:focus|'),
                'select' => $this->_p('>xhr:select|'),
                'open'   => $this->_p('>xhr:open|')
            ],
            'focus'     => $s['focus'],
            //            'selection' => $s['selection'],
            'panelName' => $this->panel->name,
        ]);

        return $v;
    }

    public function assignStatusFilter(\ewma\Views\View $v)
    {
        $statusFilter = ap($this->s, 'filters/status');
        $productsCountByStatus = ap($this->d, 'cache/products_count_by_status');

        foreach ((new \ss\moderation\Main)->statuses as $status => $data) {
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

        return $v;
    }
}
