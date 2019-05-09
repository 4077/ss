<?php namespace ss\multisource\models;

class Division extends \Model
{
    public $table = 'ss_multisource_divisions';

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }

    public function importers()
    {
        return $this->hasMany(Importer::class);
    }
}

class DivisionObserver
{
    public function creating(Division $model)
    {
        $position = Division::max('position') + 10;

        $model->position = $position;
    }
}

Division::observe(new DivisionObserver);
