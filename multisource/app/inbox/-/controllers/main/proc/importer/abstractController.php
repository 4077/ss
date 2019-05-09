<?php namespace ss\multisource\app\inbox\controllers\main\proc\importer;

abstract class AbstractController extends \Controller
{
    // resources

    protected $aiPivot;

    protected $attachment;

    protected $filePath;

    protected $tree;

    protected $rootCat;

    protected $warehouse;

    protected $importer;

    // config

    protected $stockResetMode;

    protected $productsDeleteMode;

    protected $skipRows;

    protected $articulPrefix;

    protected $articulZerofill;

    protected $productNameLevels;

    protected $ignoreCatChange;

    protected $lastRowIsProduct = true;

    protected $keyColumn;

    protected $columnsMap;

    protected $catNameColumn;

    protected $ignoreTreeView;

    protected $productColumnsCheckMap;

    protected $productRequiredColors;

    protected $productRequiredColorsColumnsMap;

    protected $productRequiredColorsBg;

    protected $productRequiredColorsFg;

    protected $rowMap;

    protected $latin1;

    //

    public function __create()
    {
        if ($aiPivot = $this->unpackModel('ai_pivot')) {
            $this->aiPivot = $aiPivot;
            $this->attachment = $aiPivot->attachment;

            // check file

            $filePath = $this->_protected('data', '~:' . $this->attachment->file_path);

            if (file_exists($filePath)) {
                $this->filePath = $filePath;
            } else {
                $this->log('ERROR: file ' . $filePath . ' does not exists');
                $this->lock();
            }

            // set config

            if ($importer = $aiPivot->importer) {
                $this->importer = $importer;

                if ($this->tree = $importer->tree) {
                    $this->rootCat = $this->getRootCat();
                } else {
                    $this->log('ERROR: tree not set');
                    $this->lock();
                }

                $this->warehouse = $importer->warehouse;
                $this->stockResetMode = $importer->stock_reset_mode;
                $this->productsDeleteMode = $importer->products_delete_mode;
                $this->skipRows = $importer->skip_rows;
                $this->articulPrefix = $importer->articul_prefix;
                $this->articulZerofill = $importer->articul_zerofll;
                $this->productNameLevels = $importer->product_name_levels;
                $this->ignoreCatChange = $importer->ignore_cat_change;
                $this->keyColumn = $importer->key_column;
                $this->columnsMap = _j($importer->import_map);
                $this->ignoreTreeView = $importer->ignore_tree_view;
                $this->rowMap = $this->renderRowMap($this->columnsMap);
                $this->latin1 = $this->attachment->encoding == 'latin1';

                if ($productRequiredColumns = $importer->product_required_columns) {
                    $this->productColumnsCheckMap = $this->renderColumnsMap(l2a($productRequiredColumns));
                }

                $productRequiredColors = _j($importer->product_required_colors);

                if (ap($productRequiredColors, 'enabled')) {
                    $this->productRequiredColors = $productRequiredColors;

                    $productRequiredColorsColumns = ap($productRequiredColors, 'columns');

                    $this->productRequiredColorsColumnsMap = $productRequiredColorsColumns ? l2a($productRequiredColorsColumns) : true;

                    $this->productRequiredColorsBg = ap($productRequiredColors, 'colors/bg');
                    $this->productRequiredColorsFg = ap($productRequiredColors, 'colors/fg');
                }
            } else {
                $this->log('ERROR: attachment ' . $aiPivot->id . ' does not have importer');
                $this->lock();
            }
        } else {
            $this->log('ERROR: not passed attachment');
            $this->lock();
        }
    }

    private function getRootCat()
    {
        $rootCat = ss()->trees->getRootCat($this->tree->id);

        if ($baseCatPath = $this->importer->base_cat_path) {
            $baseCatNameBranch = p2a($baseCatPath);

            $cat = $rootCat;

            $nameBranch = [];
            foreach ($baseCatNameBranch as $name) {
                $nameBranch[] = $name;

                $articul = jmd5($nameBranch);

                if (!$nested = $cat->nested()->where('articul', $articul)->first()) {
                    $nested = $cat->nested()->create([
                                                         'tree_id' => $this->tree->id,
                                                         'articul' => $articul,
                                                         'name'    => $name
                                                     ]);
                }

                $cat = $nested;
            }

            $rootCat = $cat;
        }

        return $rootCat;
    }

    protected function renderRowData(\PhpOffice\PhpSpreadsheet\Worksheet\Row $row)
    {
        $row = $this->renderRowArray($row);

        $rowData = [
            'articul'     => '',
            'cat_name'    => '',
            'vendor_code' => '',
            'name'        => '',
            'short_name'  => '',
            'units'       => '',
            'alt_units'   => '',
            'unit_size'   => '',
            'price'       => null,
            'stock'       => null
        ];

        remap($rowData, $row, $this->rowMap);

        return $rowData;
    }

    private function renderRowMap($columnsMap)
    {
        $columnsMap['key'] = $this->keyColumn;

        $map = [];

        foreach ($columnsMap as $field => $columnChar) {
            $map[] = $field . ' ' . (ord($columnChar) - 65);
        }

        return implode(', ', $map);
    }

    private function renderColumnsMap($columnsChars)
    {
        $map = [];

        foreach ($columnsChars as $columnChar) {
            $map[] = ord($columnChar) - 65;
        }

        return $map;
    }

    protected function renderRowArray(\PhpOffice\PhpSpreadsheet\Worksheet\Row $row)
    {
        $output = [];

        foreach ($row->getCellIterator() as $cell) {
            $cellValue = $cell->getValue();

            if ($this->latin1) {
                if (!$convertedValue = @iconv('cp1251', 'utf-8', iconv('', 'latin1', $cellValue))) {
                    $convertedValue = @mb_convert_encoding(mb_convert_encoding($cellValue, 'latin1', ''), 'utf-8', 'cp1251');
                }

                $cellValue = $convertedValue;
            }

            $output[] = trim($cellValue);
        }

        return $output;
    }

    protected function getSpreadsheet()
    {
        $filePath = $this->filePath;
        $loadFilePath = $filePath;

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($ext == 'xlsx') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        if ($ext == 'xls') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        }

        if ($ext == 'csv') {
            $pathinfo = pathinfo($filePath);

            $convertedFilePath = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-converted.' . $pathinfo['extension'];

            if (!file_exists($convertedFilePath)) {
                try {
                    write($convertedFilePath, iconv('windows-1251', 'utf-8', read($filePath)));
                } catch (\Throwable $e) {

                }
            }

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

            $loadFilePath = $convertedFilePath;
        }

        if (isset($reader)) {
            $spreadsheet = $reader->load($loadFilePath);

            return $spreadsheet;
        }
    }
}
