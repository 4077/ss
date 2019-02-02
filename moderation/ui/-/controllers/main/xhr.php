<?php namespace ss\moderation\ui\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function setStatus($status)
    {
        if ($product = $this->unxpackModel('product')) {
            if (in($status, 'discarded, temporary, scheduled')) {
                $product->status = $status;
                $product->status_datetime = \Carbon\Carbon::now()->toDateTimeString();

                if ($status == 'discarded') {
                    $product->published = false;
                    $product->enabled = false;
                }

                if ($status == 'temporary') {
                    $product->published = false;
                    $product->enabled = true;
                }

                if ($status == 'scheduled') {
                    $product->published = true;
                    $product->enabled = true;
                }

                $product->save();

                (new \ss\moderation\Main)->updateStatusFilterCache($product->tree);

                $this->c('<:reload|');

                pusher()->trigger('ss/product/update', [
                    'id'     => $product->id,
                    'status' => $status
                ]);

                pusher()->trigger('ss/product/any/update_status');
            }
        }
    }
}
