<?php namespace ss\cats\cp\product\controllers\main;

class App extends \Controller
{
    public function imagesUpdate()
    {
        $product = $this->data('product');

        $updatedProducts = ss()->products->updateImages($product);

        foreach ($updatedProducts as $updatedProduct) {
            ss()->products->dropCache($updatedProduct);

            $imagesCount = $product->images()->count();

            pusher()->trigger('ss/product/update', [
                'id'     => $updatedProduct->id,
                'images' => [
                    'has'   => (bool)$imagesCount,
                    'count' => $imagesCount
                ]
            ]);

//            pusher()->triggerOthers('ss/product/update', [
            pusher()->trigger('ss/product/update', [ // понадобилось для обновления при переносе из плагина запроса фотографий
                                                     'id'           => $updatedProduct->id,
                                                     'imagesOthers' => true
            ]);
        }
    }
}
