<?php namespace ss\models;

class Cat extends \Model
{
    use \SleepingOwl\WithJoin\WithJoinTrait; // ?

    protected $table = 'ss_cats';

    protected $guarded = []; // ?

    public $hiddenByPublisher = null;

    public function tree()
    {
        return $this->belongsTo(Tree::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function nested()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function folders()
    {
        return $this->hasMany(self::class, 'parent_id')->where('type', 'page'); // todo page>folder
    }

    public function pages()
    {
        return $this->hasMany(self::class, 'parent_id')->where('type', 'page');
    }

    public function containedPages()
    {
        return $this->hasMany(self::class, 'container_id')->where('type', 'page');
    }

    public function container()
    {
        return $this->belongsTo(self::class, 'container_id');
    }

    public function containers()
    {
        return $this->hasMany(self::class, 'parent_id')->where('type', 'container');
    }

    public function components()
    {
        return $this->belongsToMany(\ewma\components\models\Component::class, 'ss_cats_components');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'cat_id');
    }

    public function images()
    {
        return $this->morphMany(\std\images\models\Image::class, 'imageable');
    }

    public function otherMorphMany($className, $relation)
    {
        return $this->morphMany($className, $relation);
    }

    //
    // ^?
    //

    public function handlers($instance = null) // todo ?
    {
        $relation = $this->morphOne(\ewma\handlers\models\Handler::class, 'target');

        if (null !== $instance) {
            return $relation->where('instance', $instance);
        } else {
            return $relation;
        }
    }

    public function handler($instance = '') // todo ?
    {
        return $this->handlers($instance)->first();
    }

    public function users() // ?
    {
        return $this->belongsToMany(User::class, 'ss_cats_users');
    }
}

class CatObserver
{
    public function creating(Cat $model)
    {
        $position = Cat::max('position') + 10;

        $model->position = $position;
    }

    public function deleting(Cat $model)
    {
        CatComponent::where('cat_id', $model->id)->delete();
    }
}

Cat::observe(new CatObserver);
