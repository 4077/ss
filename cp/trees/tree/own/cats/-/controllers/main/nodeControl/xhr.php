<?php namespace ss\cp\trees\tree\own\cats\controllers\main\nodeControl;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function toggle($mode)
    {
        $user = \ss\models\User::find($this->s('<~:selected_user_id'));
        $cat = $this->unxpackModel('cat');

        if ($user && $cat && in($mode, 'merge, diff')) {
            ss()->own->toggleUserCatLink($user, $cat, $mode);

            $this->c('<<:reload|', [
                'tree' => $cat->tree
            ]);
        }
    }
}
