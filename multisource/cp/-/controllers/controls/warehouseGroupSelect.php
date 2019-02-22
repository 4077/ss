<?php namespace ss\multisource\cp\controllers\controls;

class WarehouseGroupSelect extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $warehouse = $this->data['warehouse'];

        $v->assign([
                       'CONTENT' => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:select',
                           'data'     => [
                               'warehouse' => xpack_model($warehouse)
                           ],
                           'items'    => [0 => '-'] + table_cells_by_id(\ss\multisource\models\WarehouseGroup::orderBy('position')->get(), 'name'),
                           'selected' => $warehouse->group_id
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
