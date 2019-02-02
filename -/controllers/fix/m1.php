<?php namespace ss\controllers\fix;

class M1 extends \Controller
{
    public function convertToNewFormat()
    {
        return false;

        $treeId = 2;

        $rootCat = ss()->trees->getRootCat($treeId);

        $tree = \ewma\Data\Tree::get(\ss\models\Cat::where('tree_id', $treeId));

        $flatten = $tree->getFlattenData($rootCat->id);

        $count = 0;

        foreach ($flatten['nodes_by_id'] as $id => $cat) {
            if ($cat->type == 'container') {
                $attached = \ss\models\CatComponent::with(['cat', 'component'])
                    ->where('cat_id', $cat->id)
                    ->where('component_id', 58)
                    ->where('type', 'renderer')
                    ->get();

                foreach ($attached as $pivot) {
                    $pivotData = _j($pivot->data);

                    remap($pivotData, $pivotData, '
                        grid/stock_minimum/enabled                  grid/common_unit_size/enabled,
                        grid/stock_minimum/value                    grid/common_unit_size/value,
                        grid/under_order_minimum/enabled            grid/common_unit_size/enabled,
                        grid/under_order_minimum/value              grid/common_unit_size/value,      
                        grid/not_in_under_order_products_display    grid/not_in_stock_products_display                  
                    ');

                    ap($pivotData, 'grid/not_in_under_order_products_display', true);

                    $pivot->data = j_($pivotData);
                    $pivot->save();

                    $this->log('update pivot: ' . $pivot->id);

                    $count++;
                }
            }
        }

        $this->log('updated pivots: ' . $count);
    }

    public function newGridComponentFormat()
    {
        return false;

        $this->c('\ss\cats~components:updateData', [
            'mode'         => AA,
            'tree_id'      => 2,
            'cat_type'     => 'container',
            'type'         => 'renderer',
            'component_id' => 58,
            'data'         => [
                'grid' => [
                    'stock_minimum'       => [
                        'enabled' => false,
                        'value'   => 1
                    ],
                    'under_order_minimum' => [
                        'enabled' => false,
                        'value'   => 1
                    ],
                ],
                'tile' => [
                    'stock_info' => [
                        'in_under_order'     => [
                            'display' => false,
                            'mode'    => 'value',
                            'label'   => 'Под заказ'
                        ],
                        'not_in_under_order' => [
                            'display' => false,
                            'mode'    => 'value',
                            'label'   => 'Нет под заказ'
                        ],
                        'common'             => [
                            'stock_value_label'       => 'В наличии:',
                            'under_order_value_label' => 'Под заказ:'
                        ]
                    ],
                    'image'      => [
                        'resize_mode' => 'fill'
                    ]
                ]
            ]
        ]);
    }
}
