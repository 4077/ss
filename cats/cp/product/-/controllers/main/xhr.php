<?php namespace ss\cats\cp\product\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($product = \ss\models\Product::find($this->data('product_id'))) {
            $this->c('<:reload', ['product' => $product], 'pivot');
        }
    }

    private function resetCache($product)
    {
        ss()->products->dropCache($product);
    }

    public function updateField()
    {
        if ($product = $this->unxpackModel('product')) {
            $field = $this->data('field');
            $value = $this->data('value');

            if (in($field, 'price, alt_price, old_price, unit_size')) {
                $value = \ss\support\Support::parseDecimal($value);

                if (is_numeric($value)) {
                    $updatedProducts = ss()->products->update($product, [
                        $field => $value
                    ]);

                    foreach ($updatedProducts as $updatedProduct) {
                        pusher()->trigger('ss/product/update', [
                            'id'        => $updatedProduct->id,
                            $field      => $updatedProduct->{$field},
                            'formatted' => $this->format($field, $updatedProduct->{$field})
                        ]);
                    }

                    $this->widget('~:|' . $product->id, 'savedHighlight', $field);

                    $this->resetCache($product);
                }
            }

            if (in($field, 'name, units, alt_units')) {
                $updatedProducts = ss()->products->update($product, [
                    $field => $value
                ]);

                if ($field == 'name') {
                    $this->c('\std\ui\dialogs~:update:editor|ss/commander', [
                        'pluginOptions' => [
                            'title' => $product->name
                        ]
                    ]);

                    ss()->products->updateSearchIndex($product);
                }

                foreach ($updatedProducts as $updatedProduct) {
                    pusher()->trigger('ss/product/update', [
                        'id'   => $updatedProduct->id,
                        $field => $updatedProduct->{$field}
                    ]);
                }

                $this->widget('~:|' . $product->id, 'savedHighlight', $field);

                $this->resetCache($product);
            }
        }
    }

    private function format($field, $value)
    {
        if (in($field, 'price, alt_price, old_price')) {
            return number_format__($value);
        }

        if ($field == 'unit_size') {
            return trim_zeros($value);
        }

        return $value;
    }

    public function reloadField()
    {
        if ($product = $this->unxpackModel('product')) {
            $field = $this->data('field');

            if (in($field, 'price, alt_price, old_price, unit_size')) {
                $this->widget('~:|', 'setFieldValue', [
                    'field' => $field,
                    'value' => $product->{$field}
                ]);
            }
        }
    }

    public function toggleEnabled()
    {
        if ($this->a('ss:moderation')) {
            if ($product = $this->unxpackModel('product')) {
                $updatedProducts = ss()->products->update($product, [
                    'enabled' => !$product->enabled
                ]);

                $catsIds = [];

                foreach ($updatedProducts as $updatedProduct) {
                    pusher()->trigger('ss/product/update', [
                        'id'      => $updatedProduct->id,
                        'enabled' => $updatedProduct->enabled
                    ]);

                    merge($catsIds, $updatedProduct->cat_id);
                }

                foreach ($catsIds as $catId) {
                    pusher()->trigger('ss/cat/update_products', [
                        'id' => $catId
                    ]);
                }
            }
        }
    }

    public function togglePublished()
    {
        if ($this->a('ss:moderation')) {
            if ($product = $this->unxpackModel('product')) {
                $updatedProducts = ss()->products->update($product, [
                    'published' => !$product->published
                ]);

                foreach ($updatedProducts as $updatedProduct) {
//                    pusher()->trigger('ss/product/' . $updatedProduct->id . '/toggle_published', [
//                        'published' => $updatedProduct->published
//                    ]);

                    pusher()->trigger('ss/product/update', [
                        'id'        => $updatedProduct->id,
                        'published' => $updatedProduct->published
                    ]);

//                    pusher()->trigger('ss/cat/' . $updatedProduct->cat_id . '/update_products');
                }
            }
        }
    }

    public function sendToModeration()
    {
        if ($product = $this->unxpackModel('product')) {
            $product->status = 'moderation';
            $product->status_datetime = \Carbon\Carbon::now()->toDateTimeString();

            $product->save();

            (new \ss\moderation\Main)->updateStatusFilterCache($product->tree);

//            commanderByLocalProduct($product)->queue->updateStatusFilterCache();

//            pusher()->trigger('ss/product/' . $product->id . '/update_status', [
//                'status' => 'moderation'
//            ]);

            pusher()->trigger('ss/product/update', [
                'id'     => $product->id,
                'status' => 'moderation'
            ]);

            pusher()->trigger('ss/product/any/update_status');
        }
    }

    public function setStatus()
    {
        if ($product = $this->unxpackModel('product')) {
            $status = $this->data('status');

            if (in($status, 'initial, moderation, discarded, temporary, scheduled')) {
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

                pusher()->trigger('ss/product/update', [
                    'id'     => $product->id,
                    'status' => $status
                ]);

                pusher()->trigger('ss/product/any/update_status');
            }
        }
    }

    public function divisionsDataDialog()
    {
        if ($product = $this->unxpackModel('product')) {
//            $this->c('\std\ui\dialogs~:open:divisionsData, ss|', [
//                'path'         => 'divisionsData:view|',
//                'data'         => [
//                    'product' => pack_model($product)
//                ],
////                'default'      => [
////                    'pluginOptions' => [
////                        'width' => 300
////                    ]
////                ]
//            ]);
        }
    }
}
