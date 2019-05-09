<?php namespace ss\multisource\models;

class Worker extends \Model
{
    public $table = 'ss_multisource_divisions_workers';

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}

class WorkerObserver
{
    public function creating(Worker $model)
    {
        $position = Worker::max('position') + 10;

        $model->position = $position;
    }
}

Worker::observe(new WorkerObserver);
