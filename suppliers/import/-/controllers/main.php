<?php namespace ss\suppliers\import\controllers;

class Main extends \Controller
{
    public function importAttachment()
    {
        if ($attachment = \ss\suppliers\messages\models\Attachment::find($this->data('attachment_id'))) {
            $filePath = $this->_protected('@messages~:' . $attachment->file_path);

            $importerData = handlers()->render($this->data('settings_handlers_cat') . ':' . $attachment->importer);

            ra($importerData, [
                'file_code' => $attachment->md5 . $attachment->sha1,
                'file_path' => $filePath
            ]);

            $imported = $this->c('>importer:import', [
                'importer'      => $attachment->importer,
                'importer_data' => $importerData
            ]);

            if ($imported) {
                $attachment->imported_at = \Carbon\Carbon::now()->toDateTimeString();
                $attachment->save();
            }
        }
    }

//    public function importNextMessageAttachment()
//    {
//        $attachment = \ss\suppliers\messages\models\Attachment::where('importer', '!=', '')
//            ->where('importer', '!=', '0')
//            ->where('importer', '!=', null)
//            ->where('imported_at', null)
//            ->orderBy('message_id')
//            ->orderBy('id')
//            ->first();
//
//        if ($attachment) {
//            $filePath = $this->_protected('@messages~:' . $attachment->file_path);
//
//            $imported = $this->c('>importer:import', [
//                'importer'  => $attachment->importer,
//                'file_code' => $attachment->md5 . $attachment->sha1,
//                'file_path' => $filePath
//            ]);
//
//            if ($imported) {
//                $attachment->imported_at = \Carbon\Carbon::now()->toDateTimeString();
//                $attachment->save();
//
//                $this->async($this->_p(':importNextMessageAttachment'));
//            }
//        }
//    }

    public function clear()
    {
//        return false;

        $importerData = handlers()->render('tdui/suppliers/import/importers:' . $this->data('importer'));

        $targetFolder = \ss\models\Cat::find(60147);

        if ($cats = $targetFolder->nested) {
            $deleteCatsIds = [];
            $deleteProductsIds = [];

            $updateCatsIds = [];

            foreach ($cats as $cat) {
                merge($deleteCatsIds, $cat->id);

                $deleteInfo = ss()->cats->getDeleteInfo($cat);

                foreach ($deleteInfo['tree'] as $row) {
                    merge($deleteCatsIds, $row['cat']->id);
                    merge($deleteProductsIds, array_keys($row['products']));

                    /**
                     * @var $refsInfo \ss\Svc\Products\RefsInfo
                     */
                    if ($refsInfo = $row['refs_info']) {
                        if ($refsIds = $refsInfo->getRefsIds()) {
                            merge($deleteProductsIds, $refsIds);
                        }
                    }
                }

                merge($updateCatsIds, $cat->parent_id);
            }

//            foreach ($updateCatsIds as $catId) {
//                pusher()->trigger('ss/cat/update_cats', [
//                    'id' => $catId
//                ]);
//            }

            \ss\models\Cat::whereIn('id', $deleteCatsIds)->delete();

            $productsBuilder = \ss\models\Product::whereIn('id', $deleteProductsIds);
            $productsBuilder->delete();
        }
    }
}
