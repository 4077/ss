<?php namespace ss\suppliers\messages\controllers\main;

class MessagesLoader extends \Controller
{
    public function __create()
    {
        $this->d('|', [
            'last_loading_datetime' => false
        ]);

        $this->dmap('|', 'settings_handler');

        mdir($this->_protected());
        mdir($this->_protected('~:'));
    }

    protected function getServer()
    {
        if ($settings = handlers()->render($this->data('settings_handler'))) {
            $server = new \Fetch\Server($settings['host'], $settings['port']);
            $server->setAuthentication($settings['user'], $settings['pass']);

            return $server;
        }
    }

    public function load()
    {
        $server = $this->getServer();

        if ($lastLoadingDatetime = $this->d(':last_loading_datetime|')) {
            $since = \Carbon\Carbon::parse($lastLoadingDatetime)->subDay()->format('d F Y');
        } else {
            $since = \Carbon\Carbon::create(2018, 11, 1)->format('d F Y');
        }

        $this->log(implode(' ', [$this->_instance(), 'SEARCH SINCE', $since]));

        /**
         * @var \Fetch\Message[] $messages
         */
        $messages = $server->search('SINCE "' . $since . '"');

        $messagesCount = count($messages);

        $this->log(implode(' ', [$this->_instance(), 'FOUND', $messagesCount]));

        $output = [];
        $replicatedCount = 0;

        foreach ($messages as $message) {
            $uid = $message->getUid();

            $messageModel = \ss\suppliers\messages\models\Message::where('instance', $this->_instance())->where('uid', $uid)->first();

            if (!$messageModel) {
                $datetime = \Carbon\Carbon::createFromTimestamp($message->getDate())->toDateTimeString();

                $from = $message->getAddresses('from');
                $subject = $message->getSubject();
                $htmlBody = $message->getHtmlBody();
                $plainTextBody = $message->getPlainTextBody();

                $messageModel = \ss\suppliers\messages\models\Message::create([
                                                                                  'instance'       => $this->_instance(),
                                                                                  'uid'            => $uid,
                                                                                  'datetime'       => $datetime,
                                                                                  'from'           => $from['address'] ?? '',
                                                                                  'subject'        => $subject ?? '',
                                                                                  'html_body'      => $htmlBody ?? '',
                                                                                  'plaintext_body' => $plainTextBody ?? ''
                                                                              ]);

                $attachmentNames = [];

                if ($attachments = $message->getAttachments()) {
                    foreach ($attachments as $attachment) {
                        $originFileName = $this->encode($attachment->getFileName());

                        $ext = pathinfo($originFileName, PATHINFO_EXTENSION);

                        if (in(strtolower($ext), 'xls, xlsx, csv, tsv')) {
                            $tmpFilePath = $this->_protected(k(16));

                            $attachment->saveAs($tmpFilePath);

                            $md5 = md5_file($tmpFilePath);
                            $sha1 = sha1_file($tmpFilePath);

                            list($dirPath, $fileName) = $this->getFingerprintPath($md5, $sha1);

                            $targetDirPath = $this->_protected('~:' . $dirPath);
                            $targetFilePath = $targetDirPath . '/' . $fileName . '.' . $ext;

                            if (!file_exists($targetFilePath)) {
                                mdir($targetDirPath);

                                rename($tmpFilePath, $targetFilePath);
                            } else {
                                unlink($tmpFilePath);
                            }

                            $messageModel->attachments()->create([
                                                                     'md5'       => $md5,
                                                                     'sha1'      => $sha1,
                                                                     'file_path' => $dirPath . '/' . $fileName . '.' . $ext,
                                                                     'file_size' => filesize($targetFilePath),
                                                                     'name'      => $originFileName,
                                                                 ]);

                            $attachmentNames[] = $originFileName;
                        }
                    }
                }

                $this->log(implode(' ', [$this->_instance(), 'REPLICATED message from: ' . ($from['address'] ?? '') . ', attachments: ' . count($attachmentNames)]));

                $output[$uid] = [
                    'uid'         => $uid,
                    'datetime'    => $datetime,
                    'attachments' => $attachmentNames
                ];

                $replicatedCount++;
            } else {
                $output[$uid] = [
                    'replicated_at' => \Carbon\Carbon::parse($messageModel->datetime)->format('d.m.Y H:i:s')
                ];
            }
        }

        if ($replicatedCount) {
            pusher()->trigger('ss/suppliers/messages/replicated');
        }

        $this->log(implode(' ', [$this->_instance(), 'REPLICATED', $replicatedCount . '/' . $messagesCount]));

        $this->d(':last_loading_datetime|', \Carbon\Carbon::now()->toDateTimeString(), RR);

        return $output;
    }

    /**
     * https://stackoverflow.com/a/29991303
     *
     * @param $str
     *
     * @return bool|string
     */
    private function encode($str)
    {
        $arrStr = explode('?', $str);

        //second part of array should be an encoding name (KOI8-R) in my case
        if (isset($arrStr[1]) && in_array($arrStr[1], mb_list_encodings())) {

            switch ($arrStr[2]) {

                case 'B': //base64 encoded
                    $str = base64_decode($arrStr[3]);
                    break;

                case 'Q': //quoted printable encoded
                    $str = quoted_printable_decode($arrStr[3]);
                    break;

            }

            //convert it to UTF-8
            $str = iconv($arrStr[1], 'UTF-8', $str);
        }

        return $str;
    }

    public static function getFingerprintPath($md5, $sha1)
    {
        $dirPath = str_split(substr($md5, 0, 8), 2);

        foreach ($dirPath as $n => $segment) { // adBlock fix
            if ($segment == 'ad') {
                $dirPath[$n] = 'ag';
            }
        }

        $fileName = substr($sha1, 0, 8);

        return [implode('/', $dirPath), $fileName];
    }

    private function clear()
    {
        return false;

        \ss\suppliers\messages\models\Message::query()->delete();
        \ss\suppliers\messages\models\Attachment::query()->delete();

        clean_dir($this->_protected());
        clean_dir($this->_protected('~:'));
    }
}
