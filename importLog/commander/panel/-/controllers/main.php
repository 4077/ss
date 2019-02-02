<?php namespace ss\importLog\commander\panel\controllers;

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

    public function __create()
    {
        if ($this->panel = commanderPanel($this->_instance())) {
            $this->tree = $this->panel->getTree();
            $this->cat = $this->panel->getCat();

//            $this->sPanel = &$this->s('~:|' . $this->_instance() . '/tree-' . $this->tree->id);

            $this->s = &$this->s('|' . $this->_instance() . '/tree-' . $this->tree->id, [
                'focus'     => false,
                'selection' => false,
                'page'      => 1,
                'per_page'  => 50,
                'filters'   => [
                    'changes_types' => [
                        'remote_cat_names_path' => true,
                        'remote_name'           => true,
                        'price'                 => true,
                        'units'                 => true
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

    public function view()
    {
        $v = $this->v('|');
        $s = &$this->s;

        ///
        $builder = \ss\models\ProductsChange::with(['product.refs', 'importRun'])->where('tree_id', $this->tree->id);

        $changeTypeFilter = ap($s, 'filters/changes_types');

        $enabledTypes = array_filter($changeTypeFilter, function ($enabled) {
            return $enabled === true;
        });

        if ($enabledTypes) {
            $builder = $builder->where(function ($query) use ($enabledTypes) {
                foreach ($enabledTypes as $field => $enabled) {
                    $query->orWhere($field . '_changed', true);
                }
            });
        }

        $changesCount = $builder->count();

        if ($changesCount <= ($s['page'] - 1) * $s['per_page']) {
            $s['page'] = floor($changesCount / $s['per_page']);
        }

        $changes = $builder->orderBy('datetime', 'DESC')
            ->offset(($s['page'] - 1) * $s['per_page'])
            ->take($s['per_page'])
            ->get();
        ///

        $n = 0;

        foreach ($changes as $change) {
            $n++;

            if ($product = $change->product) {
                $installed = count($product->refs);

                $changeDataBefore = _j($change->data_before);
                $changeDataAfter = _j($change->data_after);

                $remoteCatBefore = $changeDataBefore['remote_cat_names_path'] ?? '';
                $remoteCatAfter = $changeDataAfter['remote_cat_names_path'] ?? '';

                $remoteCatChanged = $remoteCatBefore != $remoteCatAfter;

                $v->assign('change', [
                    'N'                        => $n,
                    'CHANGE_ID'                => $change->id,
                    'PRODUCT_ID'               => $product->id,
                    'INSTALLED_CLASS'          => $installed ? 'installed' : '',
                    'REMOTE_CAT_CHANGED_CLASS' => $remoteCatChanged ? 'remote_cat_changed' : '',
                    'IMPORT_RUN_NUMBER'        => $change->importRun->number,
                    'DATE'                     => \Carbon\Carbon::parse($change->datetime)->format('d.m.Y H:i:s'),
                    'NAME'                     => $product->name,
                    'STATUS'                   => $this->statuses[$change->status],
                    'STATUS_CLASS'             => $change->status,
                    'PRICE'                    => number_format__($change->price)
                ]);
            }
        }

        $v = $this->changesTypesFilterAssign($v);

        $v->assign([
                       'FOCUS_CLASS' => $this->panel->hasFocus('plugins') ? 'focus' : '',
                       'PAGINATOR'   => $this->c('\std\ui paginator:view', [
                           'items_count' => $changesCount,
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
            '.r'        => [
                'focus'      => $this->_p('>xhr:focus|'),
                'select'     => $this->_p('>xhr:select|'),
                'install'    => $this->_p('>xhr:install|'),
                'open'       => $this->_p('>xhr:open|'),
                'diffDialog' => $this->_p('>xhr:diffDialog|')

            ],
            'focus'     => $s['focus'],
            'selection' => $s['selection'],
            'panelName' => $this->panel->name,
        ]);

        return $v;
    }

    public function changesTypesFilterAssign(\ewma\Views\View $v)
    {
        $changesTypesFilter = ap($this->s, 'filters/changes_types');
//        $productsCountByStatus = $this->d(':cache/products_count_by_status|');

        foreach ($this->changesTypes as $type => $data) {
            $v->assign('change_type_filter', [
                'BUTTON' => $this->c('\std\ui button:view', [
                    'path'  => '>xhr:toggleChangeTypeFilter|',
                    'data'  => [
                        'type' => $type
                    ],
                    'class' => 'button ' . $type . ($changesTypesFilter[$type] ? ' enabled' : ''),
                    'icon'  => 'fa ' . $data['icon'],
                    'title' => $data['title']
                    //                    'label' => $productsCountByStatus[$type] . ' '
                ])
            ]);
        }

        return $v;
    }

    private $changesTypes = [
        'remote_cat_names_path' => [
            'icon'  => 'fa-folder',
            'title' => 'Изменена категория'
        ],
        'remote_name'           => [
            'icon'  => 'fa-tag',
            'title' => 'Изменено название'
        ],
        'price'                 => [
            'icon'  => 'fa-rub',
            'title' => 'Изменена цена'
        ],
        'units'                 => [
            'icon'  => 'fa-bars',
            'title' => 'Изменены единицы измерения'
        ]
    ];

    private $statuses = [
        'created' => 'Добавлен',
        'updated' => 'Изменен'
    ];
}
