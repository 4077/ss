<?php namespace ss\suppliers\import\controllers;

class Main extends \Controller
{
    public function importAttachment()
    {
        if ($attachment = \ss\suppliers\messages\models\Attachment::find($this->data('attachment_id'))) {
            $filePath = $this->_protected('@messages~:' . $attachment->file_path);

            $importerData = handlers()->render($attachment->importer_handler);

            ra($importerData, [
                'file_code' => $attachment->md5 . $attachment->sha1,
                'file_path' => $filePath,
                'encoding'  => $attachment->encoding
            ]);

            $imported = $this->c('>importer:import', [
                'importer_data' => $importerData
            ]);

            if ($imported) {
                $attachment->imported_at = \Carbon\Carbon::now()->toDateTimeString();
                $attachment->save();
            }
        }
    }
}
