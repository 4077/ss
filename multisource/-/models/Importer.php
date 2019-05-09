<?php namespace ss\multisource\models;

class Importer extends \Model
{
    public $table = 'ss_multisource_importers';

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function tree()
    {
        return $this->belongsTo(\ss\models\Tree::class);
    }
}

class ImporterObserver
{
    public function creating(Importer $model)
    {
        $position = Importer::max('position') + 10;

        $model->position = $position;
    }
}

Importer::observe(new ImporterObserver);
