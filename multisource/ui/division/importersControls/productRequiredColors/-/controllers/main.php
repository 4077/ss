<?php namespace ss\multisource\ui\division\importersControls\productsRequiredColors\controllers;

class Main extends \Controller
{
    private $importer;

    public function __create()
    {
        if ($this->importer = $this->unpackModel('importer')) {
            $this->instance_($this->importer->id);
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

        $importer = $this->importer;
        $importerXPack = xpack_model($importer);

        $settings = _j($importer->product_required_colors);

        $enabled = ap($settings, 'enabled');

        $v->assign([
                       'TOGGLE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:toggle',
                           'data'  => [
                               'importer' => $importerXPack
                           ],
                           'class' => 'toggle_button ' . ($enabled ? 'enabled' : ''),
                           'title' => $enabled ? 'Выключить' : 'Включить',
                           'icon'  => $enabled ? 'fa fa-circle' : 'fa fa-circle-thin'
                       ]),
                       'BG_COLOR' => ap($settings, 'colors/bg'),
                       'FG_COLOR' => ap($settings, 'colors/fg')
                   ]);

        if ($enabled) {
            $v->assign('enabled', [
                'BG_TXT'      => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateColor',
                    'data'              => [
                        'importer' => $importerXPack,
                        'type'     => 'bg'
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.color',
                    'content'           => ap($settings, 'colors/bg'),
                    'title'             => 'Цвет фона'
                ]),
                'FG_TXT'      => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateColor',
                    'data'              => [
                        'importer' => $importerXPack,
                        'type'     => 'fg'
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.color',
                    'content'           => ap($settings, 'colors/fg'),
                    'title'             => 'Цвет текста'
                ]),
                'COLUMNS_TXT' => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:updateColumns',
                    'data'              => [
                        'importer' => $importerXPack
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.columns',
                    'content'           => ap($settings, 'columns'),
                    'title'             => 'Только для колонок'
                ])
            ]);
        }

        $this->css();

        return $v;
    }
}
