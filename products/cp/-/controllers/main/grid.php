<?php namespace ss\products\cp\controllers\main;

class Grid extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unxpackModel('cat')) {
            $this->instance_($this->cat->id);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $filter = $this->getFilter();
        $orderings = $this->getOrderings();

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\grid~:view|' . $this->_nodeId(), [
                           'set'      => [
                               'with'     => 'images',
                               'filter'   => $filter,
                               'ordering' => $orderings
                           ],
                           'defaults' => [
                               'model'    => \ss\models\Product::class,
                               'pager'    => ['page' => 1, 'per_page' => 10],
                               'sorter'   => [],
                               'columns'  => $this->getColumns(),
                               'ordering' => $orderings
                           ]
                       ])
                   ]);

        $this->css();

        $this->e('ss/products/create')->rebind(':reload');
        $this->e('ss/products/delete')->rebind(':reload');
        $this->e('ss/products/delete_all')->rebind(':reload');

        return $v;
    }

    private function getFilter()
    {
        return [
            'cat_id' => $this->cat->id
        ];
    }

    private function getOrderings()
    {
        return ['position' => ['cat_id', $this->cat->id]];
    }

    private function getColumns()
    {
        $columns = $this->c('app/fields:get:' . $this->_nodeId(), [
            'dialogs_container_instance' => 'ss/products'
        ]);

        $columns = unmap($columns, 'props, search_keywords, qr_code');

        return $columns;
    }
}
