<?php namespace ss\controllers\main;

class Cats extends \Controller
{
    public function getName()
    {
        $name = ss()->cats->getName($this->data('cat')) or
        $name = null;

        return $name;
    }
}
