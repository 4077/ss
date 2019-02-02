<?php namespace ss\cats\controllers;

class Sync extends \Controller
{
    public function getImagesFilesList()
    {
        $output = [];

        if ($cat = \ss\models\Cat::find($this->data('cat_id'))) {
            $catsIds = \ewma\Data\Tree::getIds($cat);

            $products = \ss\models\Product::with('images.versions')->whereIn('cat_id', $catsIds)->get();

            foreach ($products as $product) {
                foreach ($product->images as $image) {
                    foreach ($image->versions as $version) {
                        merge($output, $version->file_path);
                    }
                }
            }
        }

        return $output;
    }

    public function getListFilePath()
    {
        return $this->_protected('cat_products_images');
    }

    public function writeListFile()
    {
        $list = $this->data('list') or
        $list = $this->getImagesFilesList();

        write($this->getListFilePath(), implode(PHP_EOL, $list));
    }
}
