<?php namespace ss\cats\cp\page\common\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function reload()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $this->c('<:reload|', [
                'cat' => $cat
            ]);
        }
    }

    public function updateField()
    {
        if ($cat = $this->unxpackModel('cat')) {
            if (ss()->cats->isEditable($cat)) {
                $field = $this->data('field');
                $value = $this->data('value');

                if (in($field, $this->c('~app:getFields'))) {
                    if ($field == 'alias') {
                        $value = $this->c('\std slugify:get', ['string' => $value]);
                    }

                    $cat->{$field} = $value;
                    $cat->save();

                    if ($field == 'name' || $field == 'short_name') {
                        pusher()->trigger('ss/tree/' . $cat->tree_id . '/page/any/name');

                        pusher()->trigger('ss/page/update', [
                            'id'        => $cat->id,
                            'name'      => ss()->cats->getName($cat),
                            'shortName' => ss()->cats->getShortName($cat)
                        ]);

                        $this->se('ss/pages/any/update_name')->trigger();
                    }

                    if ($field == 'description') {
                        pusher()->trigger('ss/page/update', [
                            'id'          => $cat->id,
                            'description' => $value
                        ]);
                    }

                    if ($field == 'alias') {
                        ss()->cats->updateRouteCache($cat);

                        pusher()->trigger('ss/page/update', [
                            'id'    => $cat->id,
                            'alias' => $value,
                            'route' => $cat->route_cache
                        ]);
                    }

                    $this->widget('~:|' . $cat->id, 'savedHighlight', $field);
                }
            }
        }
    }

    public function toggleEnabled()
    {
        if ($this->a('ss:moderation')) {
            if ($cat = $this->unxpackModel('cat')) {
                $cat->enabled = !$cat->enabled;
                $cat->save();

                pusher()->trigger('ss/tree/' . $cat->tree_id . '/page/any/enabled');

                pusher()->trigger('ss/page/update', [
                    'id'      => $cat->id,
                    'enabled' => $cat->enabled
                ]);

                $this->se('ss/pages/any/toggle_enabled')->trigger();
            }
        }
    }

    public function togglePublished()
    {
        if ($this->a('ss:moderation')) {
            if ($cat = $this->unxpackModel('cat')) {
                $cat->published = !$cat->published;
                $cat->save();

                pusher()->trigger('ss/tree/' . $cat->tree_id . '/page/any/published');

                pusher()->trigger('ss/page/update', [
                    'id'        => $cat->id,
                    'published' => $cat->published
                ]);

                $this->se('ss/pages/any/toggle_published')->trigger();
            }
        }
    }
}
