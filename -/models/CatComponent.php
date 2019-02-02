<?php namespace ss\models;

class CatComponent extends \Model
{
    protected $table = 'ss_cats_components';

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function cats()
    {
        return $this->hasMany(Cat::class);
    }

    public function component()
    {
        return $this->belongsTo(\ewma\components\models\Component::class);
    }

    public function components()
    {
        return $this->hasMany(\ewma\components\models\Component::class);
    }
}

class CatComponentObserver
{
    public function creating(CatComponent $model)
    {
        $position = CatComponent::where('cat_id', $model->cat_id)->max('position') + 10;

        $model->position = $position;
    }
}

CatComponent::observe(new CatComponentObserver);
