<?php namespace ss\suppliers\messages\controllers;

class Main extends \Controller
{
    public function loadNewMessages()
    {
        $this->c_('>messagesLoader:load|');
    }

    public function detectAttachment()
    {
        if ($attachment = \ss\suppliers\messages\models\Attachment::find($this->data('attachment_id'))) {
            $detectors = $this->c('>app:getAttachmentDetectors', [
                'attachment' => $attachment
            ], 'detectors/handlers_cat');

            $this->c('>importersDetector:detect', [
                'attachment' => $attachment,
                'detectors'  => $detectors
            ]);
        }
    }

    public function detectAllAttachments()
    {
        $attachments = \ss\suppliers\messages\models\Attachment::whereNull('importer_handler')->orderBy('id')->get();

        $this->log('RUN detectAllAttachments');

        foreach ($attachments as $attachment) {
            $detectors = $this->c('>app:getAttachmentDetectors', [
                'attachment' => $attachment
            ], 'detectors/handlers_cat');

            $this->proc('>importersDetector:detect', [
                'attachment' => pack_model($attachment),
                'detectors'  => $detectors
            ])->run()->wait();
        }

        $this->log('DONE detectAllAttachments');
    }
}
