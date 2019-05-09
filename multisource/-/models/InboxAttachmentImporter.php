<?php namespace ss\multisource\models;

class InboxAttachmentImporter extends \Model
{
    public $table = 'ss_multisource_inbox_attachments_importers';

    public function attachment()
    {
        return $this->belongsTo(InboxAttachment::class);
    }

    public function importer()
    {
        return $this->belongsTo(Importer::class);
    }
}
