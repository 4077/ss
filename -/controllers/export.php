<?php namespace ss\controllers;

class Export extends \Controller
{
    public function run()
    {
        $host = $this->data('host');
        $treeId = $this->data('tree_id');

        if ($tree = \ss\models\Tree::find($treeId)) {
            $ss = ss();

            $pagesFilePath = $this->_protected('tree_' . $treeId . '_pages.csv');
            $productsFilePath = $this->_protected('tree_' . $treeId . '_products.csv');

            write($pagesFilePath, '');
            write($productsFilePath, '');

            $pagesFile = fopen($pagesFilePath, 'w');
            $productsFile = fopen($productsFilePath, 'w');

            $pages = $tree->cats()->where('enabled', true)->where('published', true)->where('type', 'page')->orderBy('name')->get();

            foreach ($pages as $page) {
                $pageName = ss()->cats->getShortName($page);

                $containers = $page->containers()->where('enabled', true)->where('published', true)->orderBy('position')->get();

                $hasVisibleProducts = false;

                foreach ($containers as $container) {
                    $products = $container->products()->where('enabled', true)->where('published', true)->orderBy('name')->get();

                    foreach ($products as $product) {
                        $image = $this->c('\std\images~:first', [
                            'model' => $product
                        ]);

                        $imageUrl = false;
                        if ($image) {
                            $imageUrl = path($host, $image->versionModel->file_path);
                        }

                        $url = path($host, 'товары', $product->id);

                        $this->putcsv($productsFile, [
                            $pageName,
                            $product->name,
                            $url,
                            $imageUrl
                        ]);

                        $hasVisibleProducts = true;
                    }
                }

                if ($hasVisibleProducts) {
                    $this->putcsv($pagesFile, [
                        $pageName
                    ]);
                }
            }

            fclose($pagesFile);
            fclose($productsFile);
        }
    }

    private function putcsv($file, $row)
    {
        $row = array_map(function ($field) {
            return iconv('utf-8', 'windows-1251', $field);
        }, $row);

        fputcsv($file, $row, ';');
    }
}
