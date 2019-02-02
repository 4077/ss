<?php namespace ss\stockPhotoRequest\ui\controllers\queue;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        $this->c('<:reload');
    }

    public function camera()
    {
        if ($request = $this->unxpackModel('request')) {
            $this->c('\std\ui\camera~:open|ss/stockPhotoRequest/queue', [
                'callbacks' => [
                    'capture' => $this->_abs('@app:capture', [
                        'imageable' => pack_model($request)
                    ])
                ]
            ]);
        }
    }
}
