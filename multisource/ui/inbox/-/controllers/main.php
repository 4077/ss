<?php namespace ss\multisource\ui\inbox\controllers;

class Main extends \Controller
{
    private $s;

    public function __create()
    {
        $this->s = &$this->s('|', [
            'page'     => 1,
            'per_page' => 15,
        ]);

        $this->packModels();
        $this->dmap('|');
        $this->unpackModels();
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    private function getMessagesBuilder()
    {
        if ($division = $this->data('division')) {
            $workers = $division->workers;

            $emails = [];

            foreach ($workers as $worker) {
                merge($emails, l2a($worker->emails));
            }

            $builder = \ss\multisource\models\Inbox::whereIn('from', $emails);
        }

        if ($mailbox = $this->data('mailbox')) {
            $builder = $mailbox->messages();
        }

        return $builder;
    }

    public function view()
    {
        pusher()->subscribe();

        $v = $this->v('|');
        $s = &$this->s;

        $builder = $this->getMessagesBuilder();

        $messagesCount = $builder->count();

        if ($messagesCount <= ($s['page'] - 1) * $s['per_page']) {
            $s['page'] = floor($messagesCount / $s['per_page']);
        }

        $messages = $builder
            ->with('attachments')
            ->offset(($s['page'] - 1) * $s['per_page'])
            ->take($s['per_page'])
            ->orderBy('datetime', 'DESC')
            ->get();

        foreach ($messages as $message) {
            $v->assign('message', [
                'ID'       => $message->id,
                'DATETIME' => \Carbon\Carbon::parse($message->datetime)->format('d.m.Y H:i:s'),
                'UID'      => $message->uid,
                'FROM'     => $message->from,
                'SUBJECT'  => $message->subject,
                //                'TITLE'    => htmlentities($message->plaintext_body)
            ]);

            foreach ($message->attachments as $attachment) {
                $v->assign('message/attachment', [
                    'ID'           => $attachment->id,
                    'XPACK'        => xpack_model($attachment),
                    'IMPORTER'     => $this->c('>importer:view', [
                        'attachment' => $attachment
                    ]),
                    'NAME'         => $attachment->name,
                    'DOWNLOAD_URL' => \ss\multisource\ui()->getRoute('mailboxes/download/' . $attachment->md5 . '/' . $attachment->sha1)
                ]);
            }
        }

        $v->assign([
                       'PER_PAGE_SELECTOR' => $this->c('\std\ui select:view', [
                           'path'     => '>xhr:setPerPage|',
                           'items'    => [5, 10, 15, 20, 25, 30, 50, 100],
                           'combine'  => true,
                           'selected' => $s['per_page']
                       ]),
                       'PAGINATOR'         => $this->c('\std\ui paginator:view', [
                           'items_count' => $messagesCount,
                           'per_page'    => $s['per_page'],
                           'page'        => $s['page'],
                           'range'       => 2,
                           'controls'    => [
                               'page'          => [
                                   '\std\ui button:view',
                                   [
                                       'path'    => $this->_p('>xhr:setPage:%page|'),
                                       'class'   => 'page_button',
                                       'content' => '%page'
                                   ]
                               ],
                               'current_page'  => [
                                   '\std\ui button:view',
                                   [
                                       'class'   => 'page_button selected',
                                       'content' => '%page'
                                   ]
                               ],
                               'skipped_pages' => [
                                   '\std\ui button:view',
                                   [
                                       'class'   => 'skipped_pages_button',
                                       'content' => '...'
                                   ]
                               ]
                           ]
                       ])
                   ]);

        $this->css();

        $this->c('\std\ui\dialogs~:addContainer:ss/multisource/division');

        $detectImportersXPid = false;
        if ($detectImportersPid = $this->d('^app/inbox~:xpids/detect_importers')) {
            if ($process = $this->app->processDispatcher->open($detectImportersPid)) {
                $detectImportersXPid = $process->getXPid();
            } else {
                $this->d('^app/inbox~:xpids/detect_importers', false, RR);
            }
        }

        $this->widget(':|', [
            '.r'                  => [
                'reloadImporter'  => $this->_p('>importer/xhr:reload'),
                'detectImporters' => $this->_p('>xhr:detectImporters'),
                'openMessage'     => $this->_p('>xhr:openMessage')
            ],
            'detectImportersXPid' => $detectImportersXPid
        ]);

        return $v;
    }
}
