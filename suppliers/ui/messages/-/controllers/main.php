<?php namespace ss\suppliers\ui\messages\controllers;

class Main extends \Controller
{
    private $s;

    public function __create()
    {
        $this->s = &$this->s('|', [
            'page'     => 1,
            'per_page' => 15,
        ]);
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        pusher()->subscribe();

        $v = $this->v('|');
        $s = &$this->s;

        $builder = \ss\suppliers\messages\models\Message::where('instance', $this->_instance());

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
                'DATETIME' => \Carbon\Carbon::parse($message->datetime)->format('d.m.Y H:i:s'),
                'FROM'     => $message->from,
                'SUBJECT'  => $message->subject,
                'TITLE'    => htmlentities($message->plaintext_body)
            ]);

            foreach ($message->attachments as $attachment) {
                if ($attachment->importer_handler) {
                    $importerData = $this->getImporterData($attachment->importer_handler);

                    $importerClass = 'detected';
                    $importerName = $importerData['name'] ?? $attachment->importer_name;
                } else {
                    if (null === $attachment->importer_handler) {
                        $importerClass = 'pending';
                        $importerName = 'ожидание';
                    } else {
                        $importerClass = 'not_detected';
                        $importerName = 'не определен';
                    }
                }

                $attachmentName = $attachment->name;
                $attachmentTitle = '';
                if (mb_strlen($attachmentName) > 50) {
                    $attachmentName = mb_substr($attachmentName, 0, 50) . '...';
                    $attachmentTitle = $attachmentName;
                }

                $v->assign('message/attachment', [
                    'FILE_CODE'      => $attachment->md5 . $attachment->sha1,
                    'IMPORTED_CLASS' => $attachment->imported_at ? 'imported' : '',
                    'IMPORTER_CLASS' => $importerClass,
                    'IMPORTER_NAME'  => $importerName,
                    'DOWNLOAD_URL'   => abs_url('cp/ss/suppliers/download-attachment/' . $attachment->md5 . '/' . $attachment->sha1),
                    'NAME'           => $attachment->id . ' ' . $attachmentName,
                    'TITLE'          => $attachmentTitle,
                    'SIZE'           => $attachment->file_size
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

        $procFileUrl = $this->_publicUrl('proc', '\ss\suppliers\import~:progress.json');

        $this->widget(':|', [
            '.r'   => [
                'reload' => $this->_p('>xhr:reload|')
            ],
            'proc' => $procFileUrl
        ]);

        return $v;
    }

    private $importerDataByHandlerPath = [];

    private function getImporterData($importerHandlerPath)
    {
        if (!isset($this->importerDataByHandlerPath[$importerHandlerPath])) {
            $this->importerDataByHandlerPath[$importerHandlerPath] = handlers()->render($importerHandlerPath);
        }

        return $this->importerDataByHandlerPath[$importerHandlerPath];
    }
}
