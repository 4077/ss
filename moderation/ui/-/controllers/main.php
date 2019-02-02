<?php namespace ss\moderation\ui\controllers;

class Main extends \Controller
{
    private $tree;

    public function __create()
    {
        if ($this->a('ss:moderation')) {
            if ($this->tree = \ss\models\Tree::find($this->_instance())) {
                // todo optimize
                $enabledPlugins = ss()->trees->plugins->getEnabled($this->tree);

                if (isset($enabledPlugins['moderation'])) {

                } else {
                    $this->lock();
                }
            } else {
                $this->lock();
            }
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

        $product = $this->tree->products()->where('status', 'moderation')->orderBy('status_datetime')->first();

        if ($product) {
            $productXPack = xpack_model($product);

            $v->assign([
                           'TILE'             => $this->tileView($product),
                           'CONFIRM_BUTTON'   => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:setStatus:scheduled|',
                               'data'    => [
                                   'product' => $productXPack
                               ],
                               'class'   => 'button confirm',
                               'content' => 'Ок',
                               'icon'    => 'fa fa-check'
                           ]),
                           'TEMPORARY_BUTTON' => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:setStatus:temporary|',
                               'data'    => [
                                   'product' => $productXPack
                               ],
                               'class'   => 'button temporary',
                               'content' => 'На доработку',
                               'icon'    => 'fa fa-exclamation-circle'
                           ]),
                           'DISCARD_BUTTON'   => $this->c('\std\ui button:view', [
                               'path'    => '>xhr:setStatus:discarded|',
                               'data'    => [
                                   'product' => $productXPack
                               ],
                               'class'   => 'button discard',
                               'content' => 'Выключить',
                               'icon'    => 'fa fa-ban'
                           ]),
                       ]);
        } else {
            $v->assign('nothing');
        }

        $this->css(':\css\std~');

        $this->c('\css\fonts~:load', [
            'fonts' => 'ptsans, roboto'
        ]);

        return $v;
    }

    private function tileView($product)
    {
        $cat = $product->cat;

        if ($pivot = ss()->cats->getFirstEnabledComponentPivot($cat)) {
            return $this->c('\ss\components\products\ui tile:view', $this->c('\ss\components\products\ui tile/app')->renderTileData($product, $pivot));
        }
    }
}
