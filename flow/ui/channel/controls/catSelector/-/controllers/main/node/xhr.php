<?php namespace ss\flow\ui\channel\controls\catSelector\controllers\main\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function toggle()
    {
        $cat = $this->unxpackModel('cat');
        $channel = $this->unxpackModel('channel');

        if ($cat && $channel) {
            $type = $this->data('type');

            $settings = _j($channel->settings);

            $cats = &ap($settings, 'cats/' . $type);

            toggle($cats, $cat->id);

            $channel->settings = j_($settings);
            $channel->save();

            pusher()->trigger('ss/flow/channel/settings/updateFilteringCats', [
                'channelId' => $channel->id
            ]);
        }
    }
}
