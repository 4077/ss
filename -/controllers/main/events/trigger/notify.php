<?php namespace ss\controllers\main\events\trigger;

// todo del

class Notify extends \Controller
{
    public function cartPageOpen()
    {
        $mailer = mailer('mailers:dev');

        $user = $this->_user();

        if ($user) {
            $userName = 'Пользователь <b>' . $user->model->login . '</b>';
        } else {
            $userName = 'Посетитель <b>' . $this->app->session->getKey() . '</b>';
        }

        $subject = 'Перешел на страницу корзины';

        $body = $userName . ' перешел на страницу корзины';

        if (is_array($body)) {
            $body = implode("<br>", $body);
        }

        $recipients = handlers()->render('tdui/mail-recipients:all-events');

        foreach ($recipients as $recipient) {
            $mailer->addAddress($recipient);
        }

        $mailer->Subject = $subject;
        $mailer->Body = $body;

        $mailer->queue();
    }

    public function createOrder()
    {

    }
}
