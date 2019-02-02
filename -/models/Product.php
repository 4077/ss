<?php namespace ss\models;

use std\images\models\Image;
use SleepingOwl\WithJoin\WithJoinTrait;

class Product extends \Model
{
    use WithJoinTrait;

    protected $table = 'ss_products';

    public function cat()
    {
        return $this->belongsTo(Cat::class);
    }

    public function tree()
    {
        return $this->belongsTo(Tree::class);
    }

    public function source()
    {
        return $this->belongsTo(self::class);
    }

    public function refs()
    {
        return $this->hasMany(self::class, 'source_id');
    }

    public function multisourceSummary()
    {
        return $this->hasMany(ProductsMultisourceSummary::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}

class ProductObserver
{
    public function creating($model)
    {
        $position = Product::max('position') + 10;

        $model->position = $position;
    }

    public function deleting(Product $model)
    {

    }
}

Product::observe(new ProductObserver);
