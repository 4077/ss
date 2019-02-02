<?php namespace ss\Svc;

class Auth extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    public function isPhoneAvailable($phone)
    {
        $user = \ss\models\User::where('phone', $phone)->first();

        return $user === null;
    }

    public function isEmailAvailable($email)
    {
        $user = \ss\models\User::where('email', $email)->first();

        return $user === null;
    }

    public function isLoginAvailable($login)
    {
        $user = \ss\models\User::where('login', $login)->first();

        return $user === null;
    }

    public function register($login = null, $email = null, $phone = null)
    {
        if (empty($login) && empty($email) && empty($phone)) {
            return false;
        }

        $userData = ['status' => 'REGISTRATION'];

        if (!empty($login)) {
            if ($this->isLoginAvailable($login)) {
                $userData['login'] = $login;
            } else {
                return false;
            }
        }

        if (!empty($email)) {
            if ($this->isEmailAvailable($email)) {
                $userData['email'] = $email;
            } else {
                return false;
            }
        }

        if (!empty($phone)) {
            if ($this->isPhoneAvailable($phone)) {
                $userData['phone'] = $phone;
            } else {
                return false;
            }
        }

        $user = \ss\models\User::create($userData);

        $user->profile()->create([]);

        return $user;
    }

    /**
     * Время до момента окончания блокировки повторной отправки смс
     *
     * @param $user
     *
     * @return mixed
     */
    public function getPassWaitingTimeout($user)
    {
        $now = \Carbon\Carbon::now();
        $lastSendingDatetime = \Carbon\Carbon::parse($user->sent_pass_datetime);

        $minInterval = dataSets()->get('ss:auth:pass_sms_min_interval');

        return $minInterval - $now->diffInSeconds($lastSendingDatetime);
    }

    /**
     * Время, оставшееся до окончания регистрации (высвобождения номера)
     *
     * @param $user
     *
     * @return mixed
     */
    public function getRegistrationTimeout($user)
    {
        $now = \Carbon\Carbon::now();
        $sentPassDatetime = \Carbon\Carbon::parse($user->sent_pass_datetime);

        $registrationTimeout = dataSets()->get('ss:auth:registration_timeout');

        return $registrationTimeout - $now->diffInSeconds($sentPassDatetime);
    }

    /**
     * Время, оставшееся до окончания восстановления
     *
     * @param $user
     *
     * @return mixed
     */
    public function getRestoringTimeout($user)
    {
        $now = \Carbon\Carbon::now();
        $sentPassDatetime = \Carbon\Carbon::parse($user->sent_pass_datetime);

        $restoringTimeout = dataSets()->get('ss:auth:restoring_timeout');

        return $restoringTimeout - $now->diffInSeconds($sentPassDatetime);
    }

    public function startPassWaiting($user)
    {
        $minInterval = dataSets()->get('ss:auth:pass_sms_min_interval');

        if ($this->getPassWaitingTimeout($user) <= $minInterval) {
            $smsPass = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

            $now = \Carbon\Carbon::now();

            $user->sent_pass = $smsPass;
            $user->sent_pass_datetime = $now->toDateTimeString();
            $user->save();

            $this->sendPassSms($user, $smsPass);

            return true;
        }
    }

    private function sendPassSms($user, $pass)
    {
        $message = $pass . ' - ваш пароль';

        appc('\sms~:send', [
            'to'      => $user->phone,
            'message' => $message
        ]);

        $row = dt(time()) . ' phone: ' . $user->phone . ' msg: ' . $message;

        $this->svc->logs->write('pass-sms', $row);
    }

    public function stopRegistrationPassWaiting($user)
    {
        $user->delete();

        if ($profile = $user->profile) {
            $profile->delete();
        }

        $row = dt(time()) . ' phone: ' . $user->phone;

        $this->svc->logs->write('canceled-registrations', $row);
    }

    public function stopRestoringPassWaiting($user)
    {
        $user->status = 'NONE';
        $user->sent_pass = '';
        $user->save();
    }
}
