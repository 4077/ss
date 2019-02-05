<?php namespace ss\models;

// todo rename to ProductChange

use SleepingOwl\WithJoin\WithJoinTrait;

class ProductsChange extends \Model
{
    use WithJoinTrait;

    protected $table = 'ss_products_changes';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function refs()
    {
        return $this->hasMany(self::class, 'product_id');
    }

    public function importRun()
    {
        return $this->belongsTo(ImportRun::class);
    }
}
