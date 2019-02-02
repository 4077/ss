<?php namespace ss\stockPhotoRequest\ui\controllers;

class Product extends \Controller
{
    private $product;

    public function __create()
    {
        if ($this->a('ss:stockPhotoRequest')) {
            if ($this->product = $this->unpackModel('product')) {
                $this->instance_($this->product->id);
            } else {
                $this->lock();
            }
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

        $s = &$this->s(false, [
            'create_panel_user_id' => false
        ]);

        if (!$s['create_panel_user_id']) {
            $s['create_panel_user_id'] = 1; // todo первый из добавленных к ветке
        }

        $product = $this->product;
        $productXPack = xpack_model($product);

        $request = \ss\stockPhotoRequest\models\Request::where('product_id', $product->id)->first();

        if ($request) {
            $requestXPack = xpack_model($request);

            $images = $this->c('\std\images~:get', [
                'model' => $request,
                'query' => '120 120 fill',
                'href'  => [
                    'enabled' => true
                ]
            ]);

            $v->assign('request', [
                'REQUEST_ICON_CLASS' => $images ? 'done fa fa-check-square-o' : 'pending fa fa-clock-o',
                'USER_SELECT'        => $this->c('\std\ui select:view', [
                    'path'     => '>xhr:changeUser',
                    'data'     => [
                        'request' => $requestXPack
                    ],
                    'items'    => table_cells_by_id(\ss\models\User::all(), 'login'), // todo users
                    'selected' => $request->to_user_id
                ]),
                'CANCEL_BUTTON'      => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:cancel',
                    'data'    => [
                        'request' => $requestXPack
                    ],
                    'class'   => 'cancel_button',
                    'content' => 'отменить'
                ]),
            ]);

            if ($images) {
                $v->assign('request/images');

                foreach ($images as $image) {
                    $imageXPack = xpack_model($image->imageModel);

                    $v->assign('request/images/image', [
                        'IMAGE'          => $image->view,
                        'ACCEPT_BUTTON'  => $this->c('\std\ui button:view', [
                            'path'  => '>xhr:acceptImage',
                            'data'  => [
                                'image'   => $imageXPack,
                                'product' => $productXPack,
                                'request' => $requestXPack
                            ],
                            'class' => 'decision_button accept',
                            'icon'  => 'fa fa-check'
                        ]),
                        'DISCARD_BUTTON' => $this->c('\std\ui button:view', [
                            'path'  => '>xhr:discardImage',
                            'data'  => [
                                'image'   => $imageXPack,
                                'product' => $productXPack,
                                'request' => $requestXPack
                            ],
                            'class' => 'decision_button discard',
                            'icon'  => 'fa fa-times'
                        ])
                    ]);
                }

                $this->c('\plugins\fancybox3~:bind', [
                    'selector'      => $this->_selector('|') . ' .request > .images',
                    'item_selector' => 'a',
                    'rel'           => underscore_model_type($request)
                ]);
            }
        } else {
            $v->assign([
                           'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:create',
                               'data'    => [
                                   'product' => $productXPack
                               ],
                               'class'   => 'create_button',
                               'content' => 'Запросить фотографию у'
                           ]),
                           'USER_SELECT'   => $this->c('\std\ui select:view', [
                               'path'     => '>xhr:selectUser',
                               'items'    => table_cells_by_id(\ss\models\User::all(), 'login'), // todo users
                               'selected' => $s['create_panel_user_id']
                           ])
                       ]);
        }

        $this->css(':\css\std~');

        $this->widget(':|', [
            '.r'        => [
                'reload' => $this->_abs('>xhr:reload', ['product' => $productXPack])
            ],
            'productId' => $product->id
        ]);

        return $v;
    }
}
