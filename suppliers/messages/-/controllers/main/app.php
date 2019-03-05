<?php namespace ss\suppliers\messages\controllers\main;

class App extends \Controller
{
    public function getAttachmentDetectors()
    {
        $attachment = $this->data('attachment');

        $email = $attachment->message->from;

        $detectorsHandlersCat = $this->data('detectors/handlers_cat');

        $handlersCat = \ewma\handlers\models\Cat::where('path', $detectorsHandlersCat)->first();

        $handlers = $handlersCat->handlers()->orderBy('position')->get();

        $output = [];

        foreach ($handlers as $handler) {
            $data = handlers()->render($handler);

            if ($data['enabled']) {
                if (in($email, $data['emails'] ?? [], true)) {
                    $output[$handler->name] = $data;
                }
            }
        }

        return $output;
    }
}
