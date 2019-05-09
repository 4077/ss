<?php namespace ss\flow\models;

class Channel extends \Model
{
    public $table = 'ss_flow_channels';

    public function source()
    {
        return $this->belongsTo(Node::class, 'source_id');
    }

    public function target()
    {
        return $this->belongsTo(Node::class, 'target_id');
    }

    public function productsConnections()
    {
        return $this->hasMany(ProductsConnection::class);
    }

    public function collation()
    {
        return $this->hasMany(Collation::class);
    }
}
