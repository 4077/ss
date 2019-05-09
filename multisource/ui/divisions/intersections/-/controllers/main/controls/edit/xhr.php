<?php namespace ss\multisource\ui\divisions\intersections\controllers\main\controls\edit;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updatePriceCoefficient()
    {
        if ($intersection = $this->unxpackModel('intersection')) {
            $txt = \std\ui\Txt::value($this);

            if (is_numeric($txt->value)) {
                $intersection->price_coefficient = $txt->value;
                $intersection->save();

                $txt->response();
            } else {
                $txt->response($intersection->price_coefficient);
            }
        }
    }

    public function delete()
    {
        if ($intersection = $this->unxpackModel('intersection')) {
           $intersection->delete();

           $this->c('~:reload');
        }
    }
}
