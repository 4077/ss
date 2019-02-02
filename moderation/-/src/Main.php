<?php namespace ss\moderation;

class Main
{
    public $statuses = [
        'initial'    => [
            'title' => 'Новый',
            'icon'  => 'fa-flash'
        ],
        'moderation' => [
            'title' => 'На модерации',
            'icon'  => 'fa-eye'
        ],
        'temporary'  => [
            'title' => 'На доработку',
            'icon'  => 'fa-exclamation-circle'
        ],
        'discarded'  => [
            'title' => 'Не прошел модерацию',
            'icon'  => 'fa-ban'
        ],
        'scheduled'  => [
            'title' => 'Плановая',
            'icon'  => 'fa-check'
        ],
    ];

    public function updateStatusFilterCache(\ss\models\Tree $tree)
    {
        $cache = [];

        foreach ($this->statuses as $status => $statusData) {
            $cache[$status] = $tree->products()->where('status', $status)->count();
        }

        appd('\ss\moderation\commander\panel~:cache/products_count_by_status|tree-' . $tree->id, $cache, RR);
    }
}
