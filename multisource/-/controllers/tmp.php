<?php namespace ss\multisource\controllers;

class Tmp extends \Controller
{
    public function inboxFix()
    {
        $messages = \ss\multisource\models\Inbox::all();

        foreach ($messages as $message) {
            $subject = $message->subject;

            $this->log($subject);

            $subject = $this->decode($subject);

            $message->subject = $subject;
            $message->save();

            $this->log($subject);
            $this->log('======================================================================================');
        }
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
}
