<?php namespace ss\cats\cp\controllers;

class Dialogs extends \Controller
{
    public function page()
    {
        $this->c('\std\ui\dialogs~:open:page, ss|', $this->getPageDialogData());
    }

    public function getPageDialogData()
    {
        $cat = $this->data['cat'];

        $dialogData = [
            'path'      => '\ss\cats\cp\page~:view|',
            'data'      => [
                'cat' => pack_model($cat)
            ],
            'title'     => $this->_abs('\ss\cats\cp\page~dialogTitle:view', [
                'cat' => pack_model($cat)
            ]),
            'default'   => [
                'pluginOptions' => [
                    'width'  => 300,
                    'height' => 400
                ]
            ],
            'callbacks' => [
                'update' => $this->_p('\ss\cats\cp\page app:updateTabDialogData')
            ]
        ];

        ra($dialogData, $this->c('\ss\cats\cp\page app:getTabDialogData'));
        ra($dialogData, $this->data('ra'));

        return $dialogData;
    }

    public function folder()
    {
        $this->c('\std\ui\dialogs~:open:page, ss|', $this->getFolderDialogData());
    }

    public function getFolderDialogData()
    {
        $cat = $this->data['cat'];

        $dialogData = [
            'path'      => '\ss\cats\cp\page~:view|',
            'data'      => [
                'cat' => pack_model($cat)
            ],
            'title'     => $this->_abs('\ss\cats\cp\page~dialogTitle:view', [
                'cat' => pack_model($cat)
            ]),
            'default'   => [
                'pluginOptions' => [
                    'width'  => 300,
                    'height' => 400
                ]
            ],
            'callbacks' => [
                'update' => $this->_p('\ss\cats\cp\page app:updateTabDialogData')
            ]
        ];

        ra($dialogData, $this->c('\ss\cats\cp\page app:getTabDialogData'));
        ra($dialogData, $this->data('ra'));

        return $dialogData;
    }

    public function container()
    {
        $this->c('\std\ui\dialogs~:open:container, ss|', $this->getContainerDialogData());
    }

    public function getContainerDialogData()
    {
        $cat = $this->data['cat'];

        $dialogData = [
            'path'      => '\ss\cats\cp\container~:view|',
            'data'      => [
                'cat' => pack_model($cat)
            ],
            'title'     => $this->_abs('\ss\cats\cp\container~dialogTitle:view', [
                'cat' => pack_model($cat)
            ]),
            'default'   => [
                'pluginOptions' => [
                    'width'  => 300,
                    'height' => 400
                ]
            ],
            'callbacks' => [
                'update' => $this->_p('\ss\cats\cp\container app:updateTabDialogData')
            ]
        ];

        ra($dialogData, $this->c('\ss\cats\cp\container app:getTabDialogData'));
        ra($dialogData, $this->data('ra'));

        return $dialogData;
    }

    public function product()
    {
        $this->c('\std\ui\dialogs~:open:product, ss|', $this->getProductDialogData());
    }

    public function getProductDialogData()
    {
        $product = $this->data['product'];

        $dialogData = [
            'path'          => '\ss\cats\cp\product~:view|',
            'data'          => [
                'product' => pack_model($product)
            ],
            //            'title'   => $this->_abs('\ss\cats\cp\product~dialogTitle:view', [
            //                'cat' => pack_model($product)
            //            ]),
            'class'         => 'padding',
            'pluginOptions' => [
                'title' => $product->name
            ],
            'default'       => [
                'pluginOptions' => [
                    'width'  => 800,
                    'height' => 600
                ]
            ]
        ];

        ra($dialogData, $this->data('ra'));

        return $dialogData;
    }
}
