<?php namespace ss\flow\models;

class Node extends \Model
{
    public $table = 'ss_flow_nodes';

    public function tree()
    {
        return $this->belongsTo(\ss\models\Tree::class);
    }
}
