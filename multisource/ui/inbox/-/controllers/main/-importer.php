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

        if ($attachment->imported_at) {
            $indicatorClass = 'imported';

            if ($importer = $attachment->importer) {
                $label = $importer->division->name . ' → ' . ($importer->name ?: '...');
            } else {
                $label = 'не определен';
            }
        } else {
            if ($attachment->detection_was_performed) {
                if ($importer = $attachment->importer) {
                    $indicatorClass = 'detected';
                    $label = $importer->division->name . ' → ' . ($importer->name ?: '...');
                } else {
                    $indicatorClass = 'not_detected';
                    $label = 'не определен';
                }
            } else {
                $indicatorClass = 'pending';
                $label = 'ожидание';
            }
        }

        $v->assign([
                       'INDICATOR_CLASS' => $indicatorClass,
                       'LABEL'           => $label
                   ]);

        $this->c('\std\ui button:bind', [
            'selector' => $this->_selector('|') . ' .indicator',
            'path'     => '>xhr:openCp',
            'data'     => [
                'attachment' => $attachmentXPack
            ]
        ]);

        $this->css(':@common');

        if (!$this->app->html->containerAdded($this->_nodeId())) {
            $this->app->html->addContainer($this->_nodeId(), $this->c('>procDispatcher:view'));
        }

        return $v;
    }
}
