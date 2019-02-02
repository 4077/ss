<?php namespace ss\stockPhotoRequest\ui\controllers\product;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($product = $this->unxpackModel('product')) {
            $this->c('<:reload', [
                'product' => $product
            ]);
        }
    }

    public function changeUser()
    {
        if ($request = $this->unxpackModel('request')) {
            if ($user = \ss\models\User::find($this->data('value'))) {
                $request->to_user_id = $user->id;
                $request->save();

                pusher()->trigger('ss/stockPhotoRequest/changeUser', [
                    'id'    => $request->id,
                    'login' => $user->login
                ]);
            }
        }
    }

    public function cancel()
    {
        if ($request = $this->unxpackModel('request')) {
            $request->delete();

            (new \ss\stockPhotoRequest\Main)->updateStatusFilterCache($request->tree_id);

            $this->c('<:reload', [
                'product' => $request->product
            ]);

            pusher()->trigger('ss/stockPhotoRequest/update', [
                'treeId'    => $request->tree_id,
                'productId' => $request->product_id
            ]);
        }
    }

    public function selectUser()
    {
        $this->s('<:create_panel_user_id', $this->data('value'), RR);
    }

    public function create()
    {
        if ($product = $this->unxpackModel('product')) {
            \ss\stockPhotoRequest\models\Request::create([
                                                             'tree_id'          => $product->tree_id,
                                                             'product_id'       => $product->id,
                                                             'from_user_id'     => $this->_user('id'),
                                                             'to_user_id'       => $this->s('<:create_panel_user_id'),
                                                             'request_datetime' => \Carbon\Carbon::now()->toDateTimeString()
                                                         ]);

            (new \ss\stockPhotoRequest\Main)->updateStatusFilterCache($product->tree_id);

            $this->c('<:reload', [
                'product' => $product
            ]);

            pusher()->trigger('ss/stockPhotoRequest/update', [
                'treeId'    => $product->tree_id,
                'productId' => $product->id
            ]);
        }
    }

    public function acceptImage()
    {
        $image = $this->unxpackModel('image');
        $product = $this->unxpackModel('product');
        $request = $this->unxpackModel('request');

        if ($image && $product && $request) {
            $product->images()->save($image); // todo сделать чтобы добавляло в конец

            if (!$request->images()->count()) {
                $request->delete();

                (new \ss\stockPhotoRequest\Main)->updateStatusFilterCache($request->tree_id);
            }

            // #1
            pusher()->trigger('ss/stockPhotoRequest/update', [
                'treeId'    => $product->tree_id,
                'productId' => $product->id
            ]);

            // #2 непонятно почему
            $this->c('\ss\cats\cp\product~app:imagesUpdate', [
                'product' => $product
            ]);
        }
    }

    public function discardImage()
    {
        $image = $this->unxpackModel('image');
        $product = $this->unxpackModel('product');
        $request = $this->unxpackModel('request');

        if ($image && $product && $request) {
            $image->delete();

            $request->images_cache = '';
            $request->save();

            pusher()->trigger('ss/stockPhotoRequest/update', [
                'treeId'    => $product->tree_id,
                'productId' => $product->id
            ]);
        }
    }
}
