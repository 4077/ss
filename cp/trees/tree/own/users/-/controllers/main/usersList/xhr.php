<?php namespace ss\cp\trees\tree\own\users\controllers\main\usersList;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function setPage($number)
    {
        if ($tree = $this->unxpackModel('tree')) {
            $s = &$this->s('~');

            $s['page'] = $number;

            $this->c('<:reload', [
                'tree' => $tree
            ]);
        }
    }

    public function select()
    {
        if ($tree = $this->unxpackModel('tree')) {
            if ($user = $this->unpackModel('user')) {
                $s = &$this->s('<~');

                $s['selected_user_id'] = $user->id;

                $this->c('<~:reload', [
                    'tree' => $tree
                ]);
            }
        }
    }
}
