<?php namespace ss\stockPhotoRequest\models;

class Request extends \Model
{
    public $table = 'ss_stock_photo_request';

    public function product()
    {
        return $this->belongsTo(\ss\models\Product::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(\ewma\access\models\User::class);
    }

    public function toUser()
    {
        return $this->belongsTo(\ewma\access\models\User::class);
    }

    public function images()
    {
        return $this->morphMany(\std\images\models\Image::class, 'imageable');
    }
}
