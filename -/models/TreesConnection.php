<?php namespace ss\models;

class TreesConnection extends \Model
{
    protected $table = 'ss_trees_connections';

    public function source()
    {
        return $this->belongsTo(Tree::class);
    }

    public function target()
    {
        return $this->belongsTo(Tree::class);
    }
}

class TreesConnectionObserver
{
    public function creating(Tree $model)
    {
        $position = TreesConnection::where('instance', $model->instance)->where('source_id', $model->source_id)->max('position') + 10;

        $model->position = $position;
    }
}

TreesConnection::observe(new TreesConnectionObserver);
