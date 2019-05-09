<?php namespace ss\flow\controllers;

class Main extends \Controller
{
    public function collate()
    {
        if ($channel = $this->data('channel')) {
            $process = $this->proc('^~proc/collation:run', [
                'channel' => pack_model($channel)
            ])->pathLock()->run();

            if ($process) {
                $this->d(':xpids/collation', $process->getPid(), RR);

                pusher()->trigger('ss/flow/channel/collationStart', [
                    'xpid' => $process->getXPid()
                ]);

                return $process;
            }
        }
    }

    public function update()
    {
        if ($channel = $this->data('channel')) {
            $process = $this->proc('^~proc/update:run', [
                'channel' => pack_model($channel)
            ])->pathLock()->run();

            if ($process) {
                $this->d(':xpids/update', $process->getPid(), RR);

                pusher()->trigger('ss/flow/channel/updateStart', [
                    'xpid' => $process->getXPid()
                ]);

                return $process;
            }
        }
    }

    ///
    ///
    ///

    public function view()
    {
        $v = $this->v('|');

        $v->assign([
                       'CONTENT' => false
                   ]);

        $this->css();

        $this->c('\js\paper~:load');

        $this->widget(':|');

        return $v;
    }
}
