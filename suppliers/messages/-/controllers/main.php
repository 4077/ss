<?php namespace ss\suppliers\messages\controllers;

class Main extends \Controller
{
    public function loadNewMessages()
    {
        $this->c_('>messagesLoader:load|');
    }
//
//    public function detectNextAttachment()
//    {
//        if ($attachment = \ss\suppliers\messages\models\Attachment::where('importer', null)->orderBy('id')->first()) {
//            $this->c('>importersDetector:detect', [
//                'attachment' => pack_model($attachment)
//            ]);
//        } else {
//            $this->async('\ss\suppliers\import~:importNextMessageAttachment');
//        }
//    }
//
    public function detectAttachment()
    {
        if ($attachment = \ss\suppliers\messages\models\Attachment::find($this->data('attachment_id'))) {
            $this->c('>importersDetector:detect', [
                'attachment' => pack_model($attachment),
            ], 'detectors');
        }
    }

//    private function detectAttachments($attachments)
//    {
//        foreach ($attachments as $attachment) {
//            $this->log('QUEUE attachment ' . $attachment->id);
//
////            $this->c('\std\queue~:add', [
////                'ttl'      => 300,
////                'call'     => $this->_abs('>importersDetector:detect', [
////                    'attachment' => pack_model($attachment)
////                ]),
////                'priority' => -20,
////                'async'    => false
////            ]);
//        }
//    }
//
//    public function detectNewAttachments()
//    {
//        $attachments = \ss\suppliers\messages\models\Attachment::where('importer', null)->orderBy('id')->get();
//
//        $this->detectAttachments($attachments);
//    }
//
//    public function detectAllAttachments()
//    {
//        $attachments = \ss\suppliers\messages\models\Attachment::orderBy('id')->get();
//
//        $this->detectAttachments($attachments);
//    }
}
