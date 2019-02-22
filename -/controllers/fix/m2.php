<?php namespace ss\controllers\fix;

class M2 extends \Controller
{
    public function component58NewFormat()
    {
//        return false;

        $component = \ewma\components\models\Component::find(58);

        $defaultData = handlers()->render(components()->getHandler($component, 'default-data'));

        $pivots = \ss\models\CatComponent::with(['cat', 'component'])
            ->where('component_id', 58)
            ->where('type', 'renderer')
            ->get();

        $count = count($pivots);
        $n = 0;

        $neCount = 0;

        foreach ($pivots as $pivot) {
            $pivotData = _j($pivot->data);

            $newData = $defaultData;

            remap($newData['grid'], $pivotData['grid'], '
                name_display,
                description_display,
                stock_minimum                
            ');

            remap($newData['tile'], $pivotData['tile'], '
                template,
                             
                name/priority                               name_priority,
            
                image/width,
                image/height,
                image/resize_mode,

                price/display                               price_display,                            
                price/rounding/enabled                      price_rounding/enabled,
                price/rounding/mode                         price_rounding/mode,
                price/zeroprice_label/mode                  zeroprice_label/mode,
                price/zeroprice_label/value                 zeroprice_label/value,
                price/discount/display                      discount_display,
                                
                units/sell_by_alt_units                     sell_by_alt_units,
                units/try_force_units                       try_force_units,
                units/other_units/display                   other_units_display,
                
                stock/rounding                              stock_info/common/rounding,
                stock/selected_group/in_stock               stock_info/in_stock,
                stock/selected_group/not_in_stock           stock_info/not_in_stock,
                stock/other_groups/in_stock                 stock_info/in_under_order,
                stock/other_groups/not_in_stock             stock_info/not_in_under_order,
                
                cartbutton/display,
                cartbutton/label,
                cartbutton/quantify                         quantify
            ');

            ap($newData, 'grid/filters/stock/enabled', !ap($pivotData, 'grid/not_in_stock_products_display'));
            ap($newData, 'grid/filters/not_zeroprice/enabled', !ap($pivotData, 'grid/zeroprice_products_display'));

            $pivot->data = j_($newData);
            $pivot->save();

            $this->log(++$n . '/' . $count);
        }
    }
}
