<?php namespace ss\controllers\main;

class Products extends \Controller
{
    public function get()
    {
        $products = [];

        if ($cat = $this->unpackModel('cat')) {
            $products = $cat->products()->orderBy('position')->get();
        }

        return $products;
    }
}
