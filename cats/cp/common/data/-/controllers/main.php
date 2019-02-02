<?php namespace ss\cats\cp\common\data\controllers;

class Main extends \Controller
{
    private $cat;

    public function __create()
    {
        if ($this->cat = $this->unpackModel('cat')) {

        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|' . $this->cat->id)->replace($this->view());
    }

    public function view()
    {
        $cat = $this->cat;
        $catPack = pack_model($cat);

        $v = $this->v('|' . $cat->id);

        $v->assign([
                       'CONTENT' => $this->c('\std\ui\data~:view|' . $this->_nodeInstance(), [
                           'read_call'  => $this->_abs('>app:readData', [
                               'cat' => $catPack
                           ]),
                           'write_call' => $this->_abs('>app:writeData', [
                               'cat' => $catPack
                           ])
                       ])
                   ]);

        $this->css();

        return $v;
    }
}
