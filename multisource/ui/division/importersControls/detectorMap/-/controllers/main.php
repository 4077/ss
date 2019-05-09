<?php namespace ss\multisource\ui\division\importersControls\detectorMap\controllers;

class Main extends \Controller
{
    private $importer;

    private $importerXPack;

    /**
     * @var \ss\multisource\ui\division\importersControls\detectorMap\ValueSvc
     */
    private $valueSvc;

    public function __create()
    {
        if ($this->importer = $this->unpackModel('importer')) {
            $this->importerXPack = xpack_model($this->importer);

            $this->valueSvc = \ss\multisource\ui\division\importersControls\detectorMap\valueSvc();

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

        $map = _j($importer->detect_map) ?? [];

        foreach ($map as $coord => $value) {
            $v->assign('cell', [
                'COORD'         => $coord,
                'COORD_TXT'     => $this->coordTxt($coord),
                'VALUE_TXT'     => $this->valueTxt($coord, $value),
                'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'  => '>xhr:delete',
                    'data'  => [
                        'coord'    => $coord,
                        'importer' => $importerXPack
                    ],
                    'class' => 'delete_button',
                    'icon'  => 'fa fa-close'
                ]),
            ]);
        }

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('|') . ' .table',
            'items_id_attr'  => 'coord',
            'path'           => '>xhr:arrange',
            'data'           => [
                'importer' => $importerXPack
            ],
            'plugin_options' => [
                'distance' => 10,
                'axis'     => 'y'
            ]
        ]);

        $v->assign([
                       'CREATE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:create',
                           'data'  => [
                               'importer' => $importerXPack
                           ],
                           'class' => 'create_button',
                           'icon'  => 'fa fa-plus'
                       ])
                   ]);

        $this->css(':\css\std~');

        return $v;
    }

    private function coordTxt($coord)
    {
        return $this->c('\std\ui txt:view', [
            'path'              => '>xhr:updateCoord',
            'data'              => [
                'coord'    => $coord,
                'importer' => $this->importerXPack
            ],
            'class'             => 'txt',
            'fitInputToClosest' => '.cell',
            'selectOnFocus'     => true,
            'content'           => $coord
        ]);
    }

    private function valueTxt($coord, $value)
    {
        list($content, $contentOnInit) = $this->valueSvc->getContent($value);

        return $this->c('\std\ui txt:view', [
            'path'                       => '>xhr:updateValue|',
            'data'                       => [
                'coord'    => $coord,
                'importer' => $this->importerXPack
            ],
            'type'                       => 'textarea',
            'class'                      => 'txt ' . $this->valueSvc->getClass($value),
            'fitInputToClosest'          => '.cell',
            'editTriggerClosestSelector' => '.cell',
            'content'                    => $content,
            'contentOnInit'              => $contentOnInit
        ]);
    }
}
