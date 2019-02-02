<?php namespace ss\stockPhotoRequest\ui\controllers;

class Notify extends \Controller
{
    private $tree;

    public function __create()
    {
        if ($this->a('ss:stockPhotoRequest')) {
            if ($this->tree = $this->unpackModel('tree')) {
                $this->instance_($this->tree->id);
            } else {
                $this->lock();
            }
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $tree = $this->tree;

        $requests = \ss\stockPhotoRequest\models\Request::where('tree_id', $tree->id)->get();

        $notViewedByUserId = [];
        $notNotifiedByUserId = [];

        foreach ($requests as $request) {
            if (!$request->viewed) {
                if (!isset($notViewedByUserId[$request->to_user_id])) {
                    $notViewedByUserId[$request->to_user_id] = 0;
                }

                $notViewedByUserId[$request->to_user_id]++;
            }

            if (!$request->notified) {
                if (!isset($notNotifiedByUserId[$request->to_user_id])) {
                    $notNotifiedByUserId[$request->to_user_id] = 0;
                }

                $notNotifiedByUserId[$request->to_user_id]++;
            }
        }

        $usersIds = array_keys($notViewedByUserId); // todo все юзеры привязанные к ветке
        merge($usersIds, array_keys($notNotifiedByUserId));

        $users = \ss\models\User::whereIn('id', $usersIds)->orderBy('login')->get();

        foreach ($users as $user) {
            $notViewedCount = $notViewedByUserId[$user->id] ?? 0;
            $notNotifiedCount = $notNotifiedByUserId[$user->id] ?? 0;

            $v->assign('user', [
                'LOGIN'              => $user->login,
                'NOT_VIEWED_COUNT'   => $notViewedCount
                    ? $notViewedCount . ' ' . ending(
                        $notViewedCount,
                        'непросмотренный запрос',
                        'непросмотренных запроса',
                        'непросмотренных запросов'
                    )
                    : 'нет непросмотренных запросов',
                'NOT_NOTIFIED_COUNT' => $notNotifiedCount
                    ? 'по ' . $notNotifiedCount . ' ' . ending(
                        $notNotifiedCount,
                        'запросу',
                        'запросам',
                        'запросам'
                    ) . ' не был уведомлен'
                    : 'был уведомлен о всех запросах',
                'SEND_BUTTON'        => $this->c('\std\ui button:view', [
                    'path'  => '>xhr:send',
                    'data'  => [
                        'user' => xpack_model($user),
                        'tree' => xpack_model($tree)
                    ],
                    'class' => 'send_button ' . (!$notNotifiedCount ? 'repeat' : ''),
                    'icon'  => 'fa fa-envelope',
                    'label' => 'Отправить'
                ])
            ]);
        }

        $this->css(':\css\std~');

        return $v;
    }
}
