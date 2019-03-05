<?php namespace ss\multisource\models;

class DivisionsIntersection extends \Model
{
    public $table = 'ss_multisource_divisions_intersections';

    public function source()
    {
        return $this->belongsTo(Division::class);
    }

    public function target()
    {
        return $this->belongsTo(Division::class);
    }
}
