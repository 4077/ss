<?php namespace ss\multisource\ui\division\importersControls\treeSelector\controllers\main\tree\node;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function select()
    {
        $importer = $this->unxpackModel('importer');
        $tree = $this->unxpackModel('node');

        if ($importer && $tree) {
            $importer->tree()->associate($tree);
            $importer->save();

            pusher()->trigger('ss/multisource/importers/treeSelect', [
                'importerId'    => $importer->id,
                'importerXPack' => xpack_model($importer)
            ]);

            $this->c('\std\ui\dialogs~:close:importerTreeSelector|ss/multisource/division');
        }
    }
}
