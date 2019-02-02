<?php namespace ss\cats\cp\container\common\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateField()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $field = $this->data('field');
            $value = $this->data('value');

            if (in($field, $this->c('~app:getFields'))) {
                $cat->{$field} = $value;
                $cat->save();

                if ($field == 'name' || $field == 'short_name') {
                    pusher()->trigger('ss/container/update', [
                        'id'        => $cat->id,
                        'name'      => ss()->cats->getName($cat),
                        'shortName' => ss()->cats->getShortName($cat)
                    ]);
                }

                if ($field == 'description') {
                    pusher()->trigger('ss/container/update', [
                        'id'          => $cat->id,
                        'description' => $value
                    ]);
                }

                $this->widget('~:|', 'savedHighlight', $field);
            }
        }
    }

    public function toggleEnabled()
    {
        if ($this->a('ss:moderation')) {
            if ($cat = $this->unxpackModel('cat')) {
                $cat->enabled = !$cat->enabled;
                $cat->save();

                pusher()->trigger('ss/page/update_containers.' . $cat->parent_id);

                pusher()->trigger('ss/container/update', [
                    'id'      => $cat->id,
                    'enabled' => $cat->enabled
                ]);
            }
        }
    }

    public function togglePublished()
    {
        if ($this->a('ss:moderation')) {
            if ($cat = $this->unxpackModel('cat')) {
                $cat->published = !$cat->published;
                $cat->save();

                pusher()->trigger('ss/container/update', [
                    'id'        => $cat->id,
                    'published' => $cat->published
                ]);
            }
        }
    }

    public function toggleOutputEnabled()
    {
        if ($cat = $this->unxpackModel('cat')) {
            $cat->output_enabled = !$cat->output_enabled;
            $cat->save();

            pusher()->trigger('ss/page/update_containers', [
                'id' => $cat->parent_id
            ]);

            pusher()->trigger('ss/container/update', [
                'id'            => $cat->id,
                'outputEnabled' => $cat->output_enabled
            ]);
        }
    }
}
