<?php namespace ss\controllers\main\events;

class Desc extends \Controller
{
    public function catShortName()
    {
        if ($cat = $this->unpackModel('cat')) {
            return $cat->short_name ?: $cat->name;
        }
    }

    public function catName()
    {
        if ($cat = $this->unpackModel('cat')) {
            return $cat->name ?: $cat->short_name;
        }
    }

    public function productName()
    {
        if ($product = $this->unpackModel('product')) {
            return $product->name;
        }
    }

    public function searchQuery()
    {
        return $this->data('query');
    }
}
