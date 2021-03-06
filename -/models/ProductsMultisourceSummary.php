<?php namespace ss\models;

use SleepingOwl\WithJoin\WithJoinTrait;

class ProductsMultisourceSummary extends \Model
{
    use WithJoinTrait;

    protected $table = 'ss_multisource_summary';

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
