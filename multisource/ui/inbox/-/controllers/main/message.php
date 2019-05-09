<?php namespace ss\multisource\ui\inbox\controllers\main;

class Message extends \Controller
{
    private $message;

    public function __create()
    {
        if ($this->message = $this->unpackModel('message')) {

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

        $message = $this->message;

        $v->assign([
                       'CONTENT' => $message->html_body ?: $message->plaintext_body
                   ]);

        $this->css();

        return $v;
    }
}
