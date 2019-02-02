<?php namespace ss\cp\trees\controllers\main\node;

class Delete extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        if ($catsCount = $this->data('cats_count')) {
            $v->assign('cats', [
                'COUNT' => $catsCount
            ]);
        }

        if ($productsCount = $this->data('products_count')) {
            $v->assign('products', [
                'COUNT' => $productsCount
            ]);
        }

        if ($refsCount = $this->data('refs_count')) {
            $v->assign('refs', [
                'COUNT' => $refsCount
            ]);
        }

        $v->assign([
                       'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:delete|',
                           'class'   => 'button delete',
                           'content' => 'Удалить'
                       ]),
                       'CANCEL_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:cancel|',
                           'class'   => 'button cancel',
                           'content' => 'Отмена'
                       ]),
                   ]);

        $this->css(':\css\std~');

        return $v;
    }
}
