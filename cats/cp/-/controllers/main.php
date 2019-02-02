<?php namespace ss\cats\cp\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        $this->packModels();
        $this->dmap('|');
        $this->unpackModels();

        if ($this->cat = $this->data('cat')) {
            $this->s(false, [
                'tree_width'  => 300,
                'tree_scroll' => [0, 0]
            ]);
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

        $cat = $this->cat;

        $sCommon = $this->s();

        $s = $this->s('|', [
            'selected_cat_id' => $cat->id
        ]);

        $selectedCat = \ss\models\Cat::find($s['selected_cat_id']);

        $v->assign([
                       'TREE_WIDTH' => $sCommon['tree_width'],
                       'TREE'       => $this->c('tree~:view|' . $this->_nodeInstance(), [
                           'cat'         => $cat,
                           'selected_id' => $s['selected_cat_id'],
                           'callbacks'   => [
                               'select' => $this->_abs('>app:onCatSelect|'),
                               'create' => $this->_abs('>app:onCatCreate|'),
                               'delete' => $this->_abs('>app:onCatDelete|'),
                           ]
                       ]),
                       'CAT'        => $this->c('cat~:view|' . $this->_nodeInstance(), [
                           'cat' => $selectedCat
                       ])
                   ]);

        $this->css();

        $this->c('\std\ui\dialogs~:addContainer:ss/cats');

        return $v;
    }
}
