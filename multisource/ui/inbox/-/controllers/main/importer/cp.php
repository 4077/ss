<?php namespace ss\multisource\ui\inbox\controllers\main\importer;

class Cp extends \Controller
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

        $v->assign([
                       'NAME'                    => $attachment->name,
                       'DOWNLOAD_URL'            => \ss\multisource\ui()->getRoute('mailboxes/download/' . $attachment->md5 . '/' . $attachment->sha1),
                       'DETECT_IMPORTERS_BUTTON' => $this->c('\std\ui button:view', [
                           'ctrl'  => [
                               'path' => '>xhr:detectImporter',
                               'data' => [
                                   'attachment' => $attachmentXPack,
                                   'sync'       => true
                               ]
                           ],
                           'path'  => '>xhr:detectImporter',
                           'data'  => [
                               'attachment' => $attachmentXPack
                           ],
                           'class' => 'detect_importers_button',
                           'icon'  => 'fa fa-refresh',
                           'title' => $attachment->detection_was_performed ? 'Распознать заново' : 'Распознать'
                       ]),
                       'REPORT'             => $this->c('>report:view', [
                           'attachment' => $attachment
                       ])
                   ]);

        $this->css(':\css\std~');

        $this->widget(':|', [
            '.r'              => [
                'reload' => $this->_p('>xhr:reload')
            ],
            'attachmentXPack' => $attachmentXPack
        ]);

        return $v;
    }
}
