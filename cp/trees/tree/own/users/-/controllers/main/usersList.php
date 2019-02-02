<?php namespace ss\cp\trees\tree\own\users\controllers\main;

class UsersList extends \Controller
{
    private $tree;

    public function __create()
    {
        $this->tree = $this->unpackModel('tree') or $this->lock();
    }

    public function reload()
    {
        $this->jquery()->replace($this->view());
    }

    public function view()
    {
        $v = $this->v();

        $s = $this->s('<');

        $builder = \ss\models\User::orderBy('login');

        if ($loginFilter = $s['login_filter']) {
            $builder = $builder->where('login', 'like', $loginFilter . '%');
        }

        $count = $builder->count();

        $page = $s['page'];
        $lastVisiblePage = ceil($count / $s['per_page']);
        if ($page > $lastVisiblePage) {
            $page = $lastVisiblePage;
        }

        $builder = $builder->offset(($page - 1) * $s['per_page'])->take($s['per_page']);

        $users = $builder->get();

        $selectedUserId = $this->s('\ss\cp\trees\tree\own~:selected_user_id');

        $treeXPack = xpack_model($this->tree);

        foreach ($users as $user) {
            $v->assign('user', [
                'BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:select',
                    'data'    => [
                        'user' => xpack_model($user),
                        'tree' => $treeXPack
                    ],
                    'class'   => 'user ' . ($selectedUserId == $user->id ? 'selected' : ''),
                    'content' => $user->login
                ])
            ]);
        }

        if ($count > $s['per_page']) {
            $v->assign('paginator', [
                'CONTENT' => $this->c('\std\ui paginator:view', [
                    'items_count' => $count,
                    'per_page'    => $s['per_page'],
                    'page'        => $page,
                    'range'       => 2,
                    'controls'    => [
                        'page'          => [
                            '\std\ui button:view',
                            [
                                'path'    => $this->_p('>xhr:setPage:%page'),
                                'data'    => [
                                    'tree' => $treeXPack
                                ],
                                'class'   => 'page_button',
                                'content' => '%page'
                            ]
                        ],
                        'current_page'  => [
                            '\std\ui button:view',
                            [
                                'class'   => 'page_button selected',
                                'content' => '%page'
                            ]
                        ],
                        'skipped_pages' => [
                            '\std\ui button:view',
                            [
                                'class'   => 'skipped_pages_button',
                                'content' => '...'
                            ]
                        ]
                    ]
                ])
            ]);
        }

        $this->css();

        return $v;
    }
}
