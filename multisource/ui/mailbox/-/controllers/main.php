<?php namespace ss\multisource\ui\mailbox\controllers;

class Main extends \Controller
{
    private $mailbox;

    public function __create()
    {
        if ($this->mailbox = $this->unpackModel('mailbox')) {

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

        $mailbox = $this->mailbox;
        $mailboxXPack = xpack_model($mailbox);

        $this->app->html->setTitle($mailbox->name);

        $v->assign([
                       'MAILBOX_SELECTOR' => $this->mailboxSelector(),
                       'INBOX'            => $this->c('@inbox~:view|mailbox-' . $mailbox->id, [
                           'mailbox' => $mailbox
                       ])
                   ]);

        $this->css();

        $updateXPid = false;
        if ($updatePid = $mailbox->update_pid) {
            if ($process = $this->app->processDispatcher->open($updatePid)) {
                $updateXPid = $process->getXPid();
            } else {
                $mailbox->update_pid = false;
                $mailbox->save();
            }
        }

        $this->widget(':|', [
            '.payload'   => [
                'mailbox' => $mailboxXPack
            ],
            '.r'         => [
                'update' => $this->_p('>xhr:update|')
            ],
            'updateXPid' => $updateXPid
        ]);

        return $v;
    }

    private function mailboxSelector()
    {
        return $this->c('\std\ui select:view', [
            'path'     => '>xhr:selectMailbox',
            'items'    => table_cells_by_id(\ss\multisource\models\Mailbox::orderBy('position')->get(), 'user'),
            'selected' => $this->mailbox->id
        ]);
    }
}
