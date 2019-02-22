<?php namespace ss\multisource\cp\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function createDivision()
    {
        \ss\multisource\models\Division::create([]);

        $this->c('<:reload');
    }

    public function createWarehouse()
    {
        if ($division = \ss\multisource\models\Division::find($this->s('<:selected_division_id'))) {
            $division->warehouses()->create([]);

            $this->c('<:reload');
        }
    }

    public function createGroup()
    {
        \ss\multisource\models\WarehouseGroup::create([]);

        $this->c('<:reload');
    }

    public function selectDivision()
    {
        if ($division = $this->unxpackModel('division')) {
            $this->s('<:selected_division_id', $division->id, RR);

            $this->c('<:reload');
        }
    }
}
