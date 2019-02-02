<?php namespace ss\cats\cp\pagesTree\controllers;

class Main extends \Controller
{
    private $cat;

    private $lastRoute;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {
            $this->instance_($this->cat->tree_id);

            if ($this->app->mode == \ewma\App\App::REQUEST_MODE_ROUTE) {
                $this->lastRoute = $this->app->route;
            }
        } else {
            $this->lock();
        }
    }

    public function performCallback($name, $data = [])
    {
        if ($callback = $this->d(':callbacks/' . $name . '|')) {
            $this->_call($callback)->ra($data)->perform();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $cat = $this->cat;

        $v->assign([
                       'CONTENT' => $this->treeView()
                   ]);

        $this->css();

        $this->widget(':|', [
            '.e'    => [
                // todo events
                'ss/tree/' . $cat->tree_id . '/update_pages'      => 'mr.reload',
                'ss/tree/' . $cat->tree_id . '/update_pages-self' => 'mr.reload',
            ],
            '.r'    => [
                'reload' => $this->_abs('>xhr:reload', [
                    'cat' => xpack_model($cat)
                ]),
            ],
            'catId' => $cat->id
        ]);

        return $v;
    }

    private function treeView()
    {
        $rootCat = ss()->cats->getRootCat($this->cat);

//        $ownCatsIds = ss()->own->getOwnCatsIds($rootCat->id, ss()->access->getUser()->model);

        return $this->c('\std\ui\tree~:view|' . $this->_nodeInstance(), [
            'node_control'  => [
                '>node:view|',
                [
                    'root_node_id' => $rootCat->id,
                    'cat'          => '%model',
                    'route'        => $this->lastRoute
                ]
            ],
            'query_builder' => $this->_abs('>app:treeQueryBuilder', [
                'cat' => pack_model($this->cat)
            ]),
            'root_node_id'  => $rootCat->id,
            'expand'        => false,
            'movable'       => false,
            'sortable'      => false,
            'callbacks'     => [
                'move' => $this->_abs('>app:moveCallback', [
                    'cat' => '%source_model'
                ]),
                'sort' => $this->_abs('>app:sortCallback', [
                    'cat' => '%parent_model'
                ])
            ],
        ]);
    }
}
