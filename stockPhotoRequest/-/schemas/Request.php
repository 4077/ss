<?php namespace ss\stockPhotoRequest\schemas;

class Request extends \Schema
{
    public $table = 'ss_stock_photo_request';

    public function blueprint()
    {
        return function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('tree_id')->default(0)->unsigned();
            $table->integer('product_id')->default(0)->unsigned();
            $table->integer('from_user_id')->default(0)->unsigned();
            $table->integer('to_user_id')->default(0)->unsigned();
            $table->boolean('viewed')->default(false);
            $table->boolean('notified')->default(false);
            $table->dateTime('request_datetime')->nullable();
            $table->dateTime('response_datetime')->nullable();
            $table->text('images_cache');
        };
    }
}
