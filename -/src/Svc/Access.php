<?php namespace ss\Svc;

class Access extends \ewma\Service\Service
{
    /**
     * @return \ss\models\User
     */
    public function getUser()
    {
        if ($user = appc()->_user()) {
            $user->model = (new \ss\models\User())->forceFill($user->model->toArray());

            return $user;
        }
    }
}
