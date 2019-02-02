<?php namespace ss\suppliers\ui\messages\controllers;

class Main extends \Controller
{
    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        pusher()->subscribe();

        $v = $this->v('|');

        $messages = \ss\suppliers\messages\models\Message::with('attachments')->where('instance', $this->_instance())->orderBy('datetime', 'DESC')->get();

        foreach ($messages as $message) {
            $v->assign('message', [
                'DATETIME' => \Carbon\Carbon::parse($message->datetime)->format('d.m.Y H:i:s'),
                'FROM'     => $message->from,
                'SUBJECT'  => $message->subject,
                'TITLE'    => htmlentities($message->plaintext_body)
            ]);

            foreach ($message->attachments as $attachment) {
                if ($attachment->importer) {
                    $importerData = $this->getImporterData($attachment->importer);

                    $importerClass = 'detected';
                    $importerName = $importerData['name'] ?? $attachment->importer;
                } else {
                    if (null === $attachment->importer) {
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
                    'NAME'           => $attachmentName,
                    'TITLE'          => $attachmentTitle,
                    'SIZE'           => $attachment->file_size
                ]);
            }
        }

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

    private $importerDataByName = [];

    private function getImporterData($importerName)
    {
        if (!isset($this->importerDataByName[$importerName])) {
            $this->importerDataByName[$importerName] = handlers()->render('tdui/suppliers/import/importers:' . $importerName);
        }

        return $this->importerDataByName[$importerName];
    }
}
