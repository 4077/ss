<?php namespace ss\suppliers\controllers;

class Router extends \Controller implements \ewma\Interfaces\RouterInterface
{
    public function getResponse()
    {
        $this->route('download/{md5}/{sha1}')->to(':download');

        return $this->routeResponse();
    }

    public function download()
    {
        $attachment = \ss\suppliers\messages\models\Attachment::where('md5', $this->data('md5'))->where('sha1', $this->data('sha1'))->first();

        if ($attachment) {
            $filePath = $this->_protected('messages~:' . $attachment->file_path);

            if (file_exists($filePath)) {
                if (ob_get_level()) {
                    ob_end_clean();
                }

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($filePath));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));

                readfile($filePath);

                exit;
            }
        }
    }
}
