<?php namespace ss\multisource\ui\division\controllers;

class Main extends \Controller
{
    private $division;

    public function __create()
    {
        if ($this->division = $this->unpackModel('division')) {

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

        $s = $this->s(false, [
            'tab' => 'workers'
        ]);

        $division = $this->division;
        $divisionXPack = xpack_model($division);

        $this->app->html->setTitle($division->name);

        $v->assign('DIVISION_SELECTOR', $this->divisionSelector());
        $v->assign('DIVISIONS_ROUTE', \ss\multisource\ui()->getRoute('divisions'));

        $tabs = $this->getTabs();

        foreach ($tabs as $tab => $tabData) {
            $v->assign('tab', [
                'BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:selectTab',
                    'data'    => [
                        'tab'      => $tab,
                        'division' => $divisionXPack
                    ],
                    'class'   => 'tab ' . ($s['tab'] == $tab ? 'selected' : ''),
                    'content' => $tabData['label']
                ])
            ]);
        }

        if ($selectedTab = $tabs[$s['tab']] ?? false) {
            $v->assign([
                           'CONTENT' => $this->_call($selectedTab['ui_call'])->ra(['division' => $division])->perform(),
                           'CLASS'   => $selectedTab['class'] ?? ''
                       ]);
        }

        $this->css();

        $this->c('\std\ui\dialogs~:addContainer:ss/multisource/division');

        return $v;
    }

    private function getTabs()
    {
        $division = $this->division;

        $tabs = [
            'workers'    => [
                'label'   => 'Сотрудники',
                'ui_call' => $this->_abs('workers~:view'),
                'class'   => 'padding'
            ],
            'warehouses' => [
                'label'   => 'Склады',
                'ui_call' => $this->_abs('warehouses~:view'),
                'class'   => 'padding'
            ],
            'importers'  => [
                'label'   => 'Загрузчики',
                'ui_call' => $this->_abs('importers~:view'),
                'class'   => 'padding'
            ],
            'inbox'      => [
                'label'   => 'Входящие',
                'ui_call' => $this->_abs('@inbox~:view|division-' . $division->id, [
                    'division' => $division
                ]),
                'class'   => 'padding'
            ]
        ];

        return $tabs;
    }

    private function divisionSelector()
    {
        return $this->c('\std\ui select:view', [
            'path'     => '>xhr:selectDivision',
            'items'    => table_cells_by_id(\ss\multisource\models\Division::orderBy('position')->get(), 'name'),
            'selected' => $this->division->id
        ]);
    }
}
