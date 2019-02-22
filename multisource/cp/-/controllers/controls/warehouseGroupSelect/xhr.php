<?php namespace ss\multisource\cp\controllers\controls\warehouseGroupSelect;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        if ($warehouse = $this->unxpackModel('warehouse')) {
            $groupId = $this->data('value');

            if ($group = \ss\multisource\models\WarehouseGroup::find($groupId)) {
                $warehouse->group_id = $groupId;
                $warehouse->save();
            }
        }
    }
}
