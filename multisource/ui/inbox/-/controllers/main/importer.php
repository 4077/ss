<?php namespace ss\multisource\ui\inbox\controllers\main;

class Importer extends \Controller
{
    private $attachment;

    private $attachmentXPack;

    public function __create()
    {
        if ($this->attachment = $this->unpackModel('attachment')) {
            $this->attachmentXPack = xpack_model($this->attachment);

            $this->instance_($this->attachmentXPack);
        } else {
            $this->lock();
        }
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $attachment = $this->attachment;
        $attachmentXPack = $this->attachmentXPack;

        $aiPivots = \ss\multisource\models\InboxAttachmentImporter::with('importer')
            ->where('attachment_id', $attachment->id)
            ->get();

        $hasMatched = false;

        foreach ($aiPivots as $pivot) {
            $importer = $pivot->importer;
            $matched = $pivot->matched;

            if ($matched) {
                $label = $importer->division->name . ' → ' . ($importer->name ?: '...');

                if ($pivot->imported_at > 0) {
                    $class = 'imported';
                } else {
                    $class = 'detected';
                }

                $v->assign('importer', [
                    'PIVOT_ID' => $pivot->id,
                    'CLASS'    => $class,
                    'LABEL'    => $label
                ]);

                $this->c('\std\ui button:bind', [
                    'selector' => $this->_selector('|') . ' .importer[pivot_id=' . $pivot->id . ']',
                    'path'     => '>xhr:openCp',
                    'data'     => [
                        'attachment' => $attachmentXPack
                    ]
                ]);

                $hasMatched = true;
            }
        }

        if (!$hasMatched) {
            if ($attachment->detection_was_performed) {
                $class = 'not_detected';
                $label = 'не определены';
            } else {
                $class = 'pending';
                $label = 'ожидание';
            }

            $v->assign('no_importers', [
                'CLASS' => $class,
                'LABEL' => $label
            ]);

            $this->c('\std\ui button:bind', [
                'selector' => $this->_selector('|') . ' .no_importers',
                'path'     => '>xhr:openCp',
                'data'     => [
                    'attachment' => $attachmentXPack
                ]
            ]);
        }

        $this->css();

        if (!$this->app->html->containerAdded($this->_nodeId())) {
            $this->app->html->addContainer($this->_nodeId(), $this->c('>procDispatcher:view'));
        }

        return $v;
    }
}
