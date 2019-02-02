<?php namespace ss\cats\cp\product\controllers;

class Main extends \Controller
{
    private $product;

    private $viewInstance;

    public function __create()
    {
        if ($this->product = $this->unpackModel('product')) {
            if (ss()->products->isEditable($this->product)) {
                $this->viewInstance = $this->product->id;
            } else {
                $this->lock();
            }
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->viewInstance)->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|' . $this->viewInstance);

        $product = $this->product;
        $productXPack = xpack_model($product);

        $branch = ss()->cats->getNamesBranch($product->cat, false);

        foreach ($branch as $id => $name) {
            $v->assign('branch_node', [
                'ID'   => $id,
                'NAME' => $name
            ]);
        }

        $toggleButtonsAccess = $this->a('ss:moderation');

        $treeModeIsFolders = ($product->tree->mode ?? false) == 'folders';

        $v->assign([
                       'BRANCH_TITLE'     => implode(' → ', $branch),
                       'TREE_CLASS'       => $product->tree->mode,
                       'TREE_ICON'        => $treeModeIsFolders ? 'fa fa-folder' : 'fa fa-file',
                       'TREE_NAME'        => $product->tree->name,
                       'PRODUCT_ID'       => $product->id,
                       'ENABLED_BUTTON'   => $this->c('\std\ui button:view', [
                           'visible' => $toggleButtonsAccess,
                           'path'    => '>xhr:toggleEnabled',
                           'data'    => [
                               'product' => $productXPack
                           ],
                           'class'   => 'button enabled ' . ($product->enabled ? 'pressed' : ''),
                           'content' => $product->enabled ? 'включен' : 'выключен'
                       ]),
                       'PUBLISHED_BUTTON' => $this->c('\std\ui button:view', [
                           'visible' => $toggleButtonsAccess,
                           'path'    => '>xhr:togglePublished',
                           'data'    => [
                               'product' => $productXPack
                           ],
                           'class'   => 'button published ' . ($product->published ? 'pressed' : ''),
                           'content' => $product->published ? 'опубликован' : 'не опубликован'
                       ]),
                       'DIVISIONS_DATA'   => $this->c('>divisionsData:view', [
                           'product' => $product
                       ]),
                       'NAME'             => htmlspecialchars($product->name),
                       'PRICE'            => $product->price,
                       'UNITS'            => $product->units,
                       'ALT_PRICE'        => $product->alt_price,
                       'ALT_UNITS'        => $product->alt_units,
                       'OLD_PRICE'        => $product->old_price,
                       'UNIT_SIZE'        => trim_zeros($product->unit_size),
                       'PROPS'            => $this->c('controls/props~:view|', [
                           'product' => $product
                       ]),
                       'IMAGES'           => $this->c('\std\images\ui~:view|ss/products/' . $product->id, [
                           'imageable' => pack_model($product),
                           'dev_info'  => false,
                           'href'      => [
                               'enabled' => true
                           ],
                           'callbacks' => [
                               'update' => $this->_abs('>app:imagesUpdate', [
                                   'product' => '%imageable'
                               ])
                           ]
                       ]),
                       'GOOGLE_LINK'      => 'https://www.google.ru/search?q=' . htmlspecialchars($product->name) . '&tbm=isch',
                       'YANDEX_LINK'      => 'https://yandex.ru/images/search?text=' . htmlspecialchars($product->name)
                   ]);

        // todo optimize
        $enabledPlugins = ss()->trees->plugins->getEnabled($product->tree);

        if (isset($enabledPlugins['moderation'])) {
            if ($product->status != 'moderation') {
                $v->assign('SEND_TO_MODERATION_BUTTON', $this->c('\std\ui button:view', [
                    'path'    => '>xhr:sendToModeration',
                    'data'    => [
                        'product' => $productXPack
                    ],
                    'class'   => 'button send_to_moderation ',
                    'content' => 'Отправить на модерацию'
                ]));
            }

            if ($this->a('ss:moderation')) {
                $statuses = (new \ss\moderation\Main)->statuses;

                $v->assign('moderation', [
                    'STATUS_CLASS' => $product->status,
                    'ICON_CLASS'   => 'fa ' . $statuses[$product->status]['icon']
                ]);

                foreach ($statuses as $statusName => $statusData) {
                    if ($statusName != $product->status) {
                        $v->assign('moderation/status', [
                            'BUTTON' => $this->c('\std\ui button:view', [
                                'path'  => '>xhr:setStatus|',
                                'data'  => [
                                    'product' => $productXPack,
                                    'status'  => $statusName
                                ],
                                'class' => 'button ' . $statusName,
                                'icon'  => 'fa ' . $statusData['icon'],
                                'label' => $statusData['title']
                            ])
                        ]);
                    }
                }
            }
        }

        if (isset($enabledPlugins['stockPhotoRequest'])) {
            $v->assign('stock_photo_request', [
                'CONTENT' => $this->c('\ss\stockPhotoRequest\ui product:view', [
                    'product' => $product
                ])
            ]);
        }

        $source = $product->source;

        foreach ($this->infoFields as $field) {
            if (!empty($fieldData['source'])) {
                $model = $source;
            } else {
                $model = $product;
            }

            $v->assign('info_field', [
                'LABEL' => $field['label'],
                'VALUE' => $model->{$field['field']}
            ]);
        }

        if ($this->isSuperuser()) {
            $v->assign('info_field', [
                'LABEL' => 'search index',
                'VALUE' => $model->search_index
            ]);
        }

        $this->css(':\css\std~');

        $this->c('\css\fonts~:load', [
            'fonts' => 'ptsans, roboto'
        ]);

        $this->widget(':|' . $this->viewInstance, [
//            '.e'        => [
//                'ss/product/' . $product->id . '/update_images-others' => 'mr.reload',
//                'ss/product/' . $product->id . '/update_status'        => 'mr.reload',
//            ],
'.r'        => [
    'updateField' => $this->_p('>xhr:updateField|'),
    'reloadField' => $this->_p('>xhr:reloadField|'),
    'reload'      => $this->_abs('>xhr:reload', [
        'product_id' => $product->id
    ])
],
'productId' => $product->id,
'product'   => $productXPack
        ]);

        return $v;
    }

    private $infoFields = [
        [
            'field' => 'id',
            'label' => 'id'
        ],
        [
            'field' => 'articul',
            'label' => 'Артикул'
        ],
        [
            'field' => 'vendor_code',
            'label' => 'Код производителя'
        ],
        [
            'source' => true,
            'field'  => 'remote_name',
            'label'  => 'Оригинальное наименование'
        ],
        [
            'source' => true,
            'field'  => 'remote_short_name',
            'label'  => 'Оригинальное короткое наименование'
        ],
        //        [
        //            'source' => true,
        //            'field'  => 'stock',
        //            'label'  => 'Остаток на складе'
        //        ],
    ];
}
