<?php namespace ss\models;

use SleepingOwl\WithJoin\WithJoinTrait;

class ImportRun extends \Model
{
    use WithJoinTrait;

    protected $table = 'ss_import_runs';
}
