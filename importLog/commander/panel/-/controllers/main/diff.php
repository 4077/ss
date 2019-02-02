<?php namespace ss\importLog\commander\panel\controllers\main;

class Diff extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        if ($change = $this->unpackModel('change')) {
            $v = $this->v('|');

            $v->assign([
                           'BEFORE' => $this->c('\std\ui\data~:view|' . path($this->_nodeInstance(), 'before'), [
                               'read_call' => $this->_abs('>app:readDataBefore', [
                                   'change' => pack_model($change)
                               ])
                           ]),
                           'AFTER'  => $this->c('\std\ui\data~:view|' . path($this->_nodeInstance(), 'after'), [
                               'read_call' => $this->_abs('>app:readDataAfter', [
                                   'change' => pack_model($change)
                               ])
                           ])
                       ]);

            $this->css();

            return $v;
        }
    }
}
