<?php namespace ss\models;

class Tree extends \Model
{
    use \SleepingOwl\WithJoin\WithJoinTrait; // ?

    protected $table = 'ss_trees';

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function nested()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function cats()
    {
        return $this->hasMany(Cat::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function componentsCats($catType)
    {
        return $this->belongsToMany(\ewma\components\models\Cat::class, 'ss_trees_components_cats')->where('cat_type', $catType)->withPivot('mode');
    }
}

class TreeObserver
{
    public function creating(Tree $model)
    {
        $position = Tree::max('position') + 10;

        $model->position = $position;
    }

    public function deleting(Tree $model)
    {

    }
}

Tree::observe(new TreeObserver);
