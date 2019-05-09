<?php namespace ss\multisource\ui\division\importersControls\importerMap\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function updateExpression()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $field = $this->data('field');

            $txt = \std\ui\Txt::value($this);

            $map = _j($importer->import_map);

            $map[$field] = $txt->value;

            $importer->import_map = j_($map);
            $importer->save();

            $txt->response();
        }
    }
}
