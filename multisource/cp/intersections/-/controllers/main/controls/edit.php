<?php namespace ss\multisource\cp\intersections\controllers\main\controls;

class Edit extends \Controller
{
    private $intersection;

    public function __create()
    {
        $this->intersection = $this->data('intersection');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $intersection = $this->intersection;
        $intersectionXPack = xpack_model($intersection);

        $v->assign([
                       'TXT'           => $this->c('\std\ui txt:view', [
                           'path'                       => '>xhr:updatePriceCoefficient',
                           'data'                       => [
                               'intersection' => $intersectionXPack
                           ],
                           'fitInputToClosest'          => '.cell',
                           'editTriggerClosestSelector' => '.cell',
                           'class'                      => 'txt',
                           'content'                    => $intersection->price_coefficient
                       ]),
                       'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                           'path'  => '>xhr:delete',
                           'data'  => [
                               'intersection' => $intersectionXPack
                           ],
                           'class' => 'delete_button',
                           'icon'  => 'fa fa-close'
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
