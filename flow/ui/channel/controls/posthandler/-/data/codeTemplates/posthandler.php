<?php namespace {NAMESPACE};

class {CLASS_NAME} extends \Controller
{
    public $singleton = true;

    public function __create()
    {

    }

    public function __recreate()
    {

    }

    public function handle(\ss\models\Product $source, \ss\models\Product $target)
    {
        $multisourceCache = _j($source->multisource_cache);

        ss()->products->updateMultisourceData($target, [
            'division_id'  => null,
            'warehouse_id' => null,
            'price'        => ($multisourceCache[0]['price'] ?? null) * 1,
            'discount'     => ($multisourceCache[0]['discount'] ?? null),
            'stock'        => ($multisourceCache[0]['warehouses'][0]['stock'] ?? null),
            'reserved'     => ($multisourceCache[0]['warehouses'][0]['reserved'] ?? null)
        ]);
    }
}
