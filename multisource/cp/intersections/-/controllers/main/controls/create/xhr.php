<?php namespace ss\multisource\cp\intersections\controllers\main\controls\create;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function create()
    {
        $source = $this->unxpackModel('source');
        $target = $this->unxpackModel('target');

        if ($source && $target) {
            ss()->multisource->divisionsIntersections->create($source, $target);

            $this->c('~:reload');
        }
    }
}
