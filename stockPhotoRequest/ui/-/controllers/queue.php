<?php namespace ss\stockPhotoRequest\ui\controllers;

class Queue extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        pusher()->subscribe();

        $v = $this->v('|');

        $requests = \ss\stockPhotoRequest\models\Request::with(['product', 'images'])->where('to_user_id', $this->_user('id'))->orderBy('request_datetime')->get();

        $loadedRequestsIds = [];

        foreach ($requests as $request) {
            if ($product = $request->product) {
                $v->assign('request', [
                    'ID'            => $request->id,
                    'ARTICUL'       => $product->articul,
                    'NAME'          => $product->name,
                    'REMOTE_NAME'   => $product->remote_name,
                    'CAMERA_BUTTON' => $this->c('\std\ui button:view', [
                        'path'  => '>xhr:camera',
                        'data'  => [
                            'request' => xpack_model($request)
                        ],
                        'class' => 'camera_button',
                        'icon'  => 'fa fa-video-camera'
                    ]),
                    'PENDING_CLASS' => count($request->images) == 0 ? 'pending' : ''
                ]);

                $loadedRequestsIds[] = $request->id;
            } else {
                $request->delete();
            }
        }

        \ss\stockPhotoRequest\models\Request::whereIn('id', $loadedRequestsIds)->update([
                                                                                            'viewed' => true
                                                                                        ]);

        $this->css();

        $this->widget(':|', [
            '.r' => [
                'camera' => $this->_p('>xhr:camera'),
                'reload' => $this->_p('>xhr:reload'),
            ]
        ]);

        return $v;
    }
}
