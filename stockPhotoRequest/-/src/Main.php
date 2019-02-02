<?php namespace ss\stockPhotoRequest;

class Main
{
    public $statuses = [
        'pending' => [
            'title' => 'Ожидающие',
            'icon'  => 'fa-clock-o'
        ],
        'done'    => [
            'title' => 'Готовые',
            'icon'  => 'fa-check-square-o'
        ]
    ];

    public function updateStatusFilterCache($treeId)
    {
        $cache = [
            'pending' => \ss\stockPhotoRequest\models\Request::where('tree_id', $treeId)->where('response_datetime', '=', 0)->count(),
            'done'    => \ss\stockPhotoRequest\models\Request::where('tree_id', $treeId)->where('response_datetime', '>', 0)->count()
        ];

        appd('\ss\stockPhotoRequest\commander\panel~:cache/requests_count_by_status|tree-' . $treeId, $cache, RR);
    }
}
