<?php namespace ss\models;

use SleepingOwl\WithJoin\WithJoinTrait;

class User extends \ewma\access\models\User
{
    use WithJoinTrait;

    public function cats()
    {
        return $this->belongsToMany(Cat::class, 'ss_cats_users')->withPivot('mode');
    }
}
