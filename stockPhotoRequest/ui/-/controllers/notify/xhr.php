<?php namespace ss\stockPhotoRequest\ui\controllers\notify;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function send()
    {
        $tree = $this->unxpackModel('tree');
        $user = $this->unxpackModel('user');

        if ($tree && $user) {
            $shortcode = new \std\shortcodes\Shortcode($this);

            $shortcode
                ->ttl(86400 * 3)
                ->addCall('\std\auth app:loginUser', [
                    'user' => pack_model($user)
                ])
                ->addCall('\ewma\handlers\std~:redirect', [
                    'url'  => abs_url('cp/ss/spr'),
                    'code' => 302
                ]);

            $linkCode = $shortcode->create();

//            $message = 'Просьба сделать фотографии товаров по списку: ' . abs_url('-/' . $linkCode);
            $message = abs_url('-/' . $linkCode);

            $mailer = mailer('mailers:dev');

            $mailer->addAddress('platinumaccount@yandex.ru');
            $mailer->Subject = 'Ссылка для входа';

            $mailer->Body = $message;

            $mailer->queue();

//            $message = 'https://crm.теплыйдом38.рф/cp/ss/spr';
//
            $this->c('\sms~:send', [
                'to'      => $user->phone,
                'message' => $message
            ]);

            \ss\stockPhotoRequest\models\Request::where('tree_id', $tree->id)->where('to_user_id', $user->id)->update(['notified' => true]);

            $this->c('<:reload', [
                'tree' => $tree
            ]);
        }
    }
}
