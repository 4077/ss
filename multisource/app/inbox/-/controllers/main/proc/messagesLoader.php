<?php namespace ss\multisource\app\inbox\controllers\main\proc;

class MessagesLoader extends \Controller
{
    public function __create()
    {
        mdir($this->_protected());
        mdir($this->_protected('data', '~:'));
    }

    public function run()
    {
        $process = process();

        $mailbox = $this->unpackModel('mailbox');
        $since = $this->data('since');

        $this->log(implode(' ', [$mailbox->user, 'SEARCH SINCE', $since]));

        $process->output(['status' => 'Подключение...']);

        $server = $this->getServer($mailbox);

        try {
            $process->output(['status' => 'Получение писем...']);

            /**
             * @var \Fetch\Message[] $messages
             */
            $messages = $server->search('SINCE "' . $since . '"');
        } catch (\Exception $e) {
            $messages = [];

            $process->error('Ошибка подключения');
        }

        $messagesCount = count($messages);

        $this->log(implode(' ', [$mailbox->user, 'FOUND', $messagesCount]));

        $replicatedCount = 0;

        $n = 0;

        foreach ($messages as $message) {
            if (true === $process->handleIteration()) {
                break;
            }

            $n++;

            $uid = $message->getUid();

            $messageModel = $mailbox->messages()->where('uid', $uid)->first();

            if (!$messageModel) {
                $datetime = \Carbon\Carbon::createFromTimestamp($message->getDate())->toDateTimeString();

                $from = $message->getAddresses('from');

                $subject = $message->getSubject();
                $htmlBody = $message->getHtmlBody();
                $plainTextBody = $message->getPlainTextBody();

                $subject = $this->decode($subject);
                $htmlBody = $this->decode($htmlBody);
                $plainTextBody = $this->decode($plainTextBody);

                $messageModel = $mailbox->messages()->create([
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
                        $originFileName = $this->decodePercentFormat($attachment->getFileName());

                        $ext = pathinfo($originFileName, PATHINFO_EXTENSION);

                        if (in(strtolower($ext), 'xls, xlsx, csv, tsv')) { // todo >>importable
                            $tmpFilePath = $this->_protected(k(16));

                            $attachment->saveAs($tmpFilePath);

                            $md5 = md5_file($tmpFilePath);
                            $sha1 = sha1_file($tmpFilePath);

                            list($dirPath, $fileName) = $this->getFingerprintPath($md5, $sha1);

                            $targetDirPath = $this->_protected('data', '~:' . $dirPath);

                            $targetFilePath = $targetDirPath . '/' . $fileName . '.' . $ext;

                            if (!file_exists($targetFilePath)) {
                                mdir($targetDirPath);

                                rename($tmpFilePath, $targetFilePath);
                            } else {
                                unlink($tmpFilePath);
                            }

                            $messageModel->attachments()->create([
                                                                     // todo <<importable
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

                $this->log(implode(' ', [$mailbox->user, 'REPLICATED message from: ' . ($from['address'] ?? '') . ', attachments: ' . count($attachmentNames)]));

                $replicatedCount++;
            }
        }

        $process->output(['status' => 'Новых писем: ' . $replicatedCount]);

        if ($replicatedCount) {
            pusher()->trigger('ss/multisource/inbox/replicated', [
                'mailbox_id' => $mailbox->id //
            ]);
        }

        $this->log(implode(' ', [$mailbox->user, 'REPLICATED', $replicatedCount . '/' . $messagesCount]));

        $mailbox->update_pid = false;
        $mailbox->save();
    }

    private function getServer($mailbox)
    {
        $server = new \Fetch\Server($mailbox->host, $mailbox->port);
        $server->setAuthentication($mailbox->user, $mailbox->pass);

        return $server;
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

    public function decode($input)
    {
        $output = $input;

        $encodings = array_map('strtolower', mb_list_encodings());

        preg_match_all('/=\?(.*)\?(Q|B)\?(.*)\?=/Us', $input, $encodedParts, PREG_SET_ORDER);

        foreach ($encodedParts as $encodedPart) {
            $original = $encodedPart[0];
            $encoding = strtolower($encodedPart[1]);
            $type = $encodedPart[2];
            $encoded = $encodedPart[3];

            $decoded = $encoded;

            if (in_array($encoding, $encodings)) {
                if ($type == 'B') {
                    $decoded = base64_decode($decoded);
                }

                if ($type == 'Q') {
                    $decoded = quoted_printable_decode($decoded);
                }

                $decoded = iconv($encoding, 'UTF-8', $decoded);

                $output = str_replace($original, $decoded, $output);
            }
        }

        return $output;
    }

    public function decodePercentFormat($input)
    {
        $encodings = array_map('strtolower', mb_list_encodings());

        if (preg_match('/(.*)\'\'(.*)/', $input, $match)) {
            $encoding = strtolower($match[1]);
            $encoded = $match[2];

            $decoded = $encoded;

            if (in_array($encoding, $encodings)) {
                $decoded = urldecode($decoded);
            }

            $decoded = iconv($encoding, 'UTF-8', $decoded);

            return $decoded;
        } else {
            return $input;
        }
    }
}
