<?php namespace ss\suppliers\import\controllers\main\importer;

abstract class AbstractImporterController extends \Controller
{
    protected $importer;

    protected $fileCode;

    abstract protected function import();

    private $filePath;

    public $latin1;

    public $importerPath;

    public $tree;

    public $warehouse;

    public $articulPrefix;

    public $articulZerofill;

    public $productLevels;

    public $remoteKeyField;

    public $skipRows;

    public $baseCatId;

    public $columnsMap;

    public $productCheckMap;

    public $catNameColumn;

    public $createMap;

    public $updateMap;

    public $priceMultiplier;

    public $callbacks;

    protected $rowMap;

    private $rowCheckColumnsNumbers;

    protected $procFilePath;

    protected $procFileUrl;

    public $createCats;

    public $updateCats;

    public $createProducts;

    public $updateProducts;

    public $updateProductsCats;

    public $testRows;

    public function __create()
    {
        $this->filePath = $this->data('file_path');

        if (!file_exists($this->filePath)) {
            $this->log('FILE NOT EXISTS ' . $this->filePath);

            $this->lock();
        } else {
            $this->tree = \ss\models\Tree::find($this->data('tree_id')) or $this->lock();

            if ($this->warehouse = \ss\multisource\models\Warehouse::find($this->data('warehouse_id'))) {
                $this->latin1 = $this->data('encoding') == 'latin1';

                \ewma\Data\Data::extract($this, $this->data, '
                    importerPath        importer_path,
                    articulPrefix       articul_prefix,
                    remoteKeyField      remote_key_field,
                    skipRows            skip_rows,
                    baseCatId           base_cat_id,
                    columnsMap          columns_map,
                    productCheckMap     product_check_map,
                    catNameColumn       cat_name_column,
                    createMap           create_map,
                    updateMap           update_map,
                    priceMultiplier     price_multiplier,
                    articulZerofill     articul_zerofill,
                    productLevels       product_levels,
                    callbacks           callbacks,
                    createCats          actions/cats/create,
                    updateCats          actions/cats/update,
                    createProducts      actions/products/create,
                    updateProducts      actions/products/update,
                    updateProductsCats  actions/products/update_cats,
                    testRows            test_rows
                ');

                $this->testRows = l2a($this->testRows);

                $this->fileCode = $this->data('file_code');
                $this->procFilePath = $this->_public('proc', '~:progress.json');
                $this->procFileUrl = $this->_publicUrl('proc', '~:progress.json');
            } else {
                $this->lock();
            }
        }
    }

    public function log($content, $path = false)
    {
        parent::log($this->importer . ' ' . $content);
    }

    public function publicProcFileUpdate($data)
    {
        jwrite($this->procFilePath, $data);
    }

    private function isProcessRunning($lockFile)
    {
        return !flock($lockFile, LOCK_EX | LOCK_NB);
    }

    public function handle()
    {
        mdir($this->_protected('~:locks/' . $this->importer));

        $lockFile = fopen($this->_protected('~:locks/' . $this->importer . '.lock'), 'w');

        if ($this->isProcessRunning($lockFile)) {
            return $this->importer . ' importer already running';
        } else {
            $this->log('HANDLED tree=' . $this->tree->id . ' file=' . $this->filePath);

            $this->renderRowMap();
            $this->renderCheckColumnsNumbers();

            $imported = $this->import();

            $this->log('COMPLETED tree=' . $this->tree->id . ' file=' . $this->filePath);

            $this->performCallback('complete');

            return $imported;
        }
    }

    public function performCallback($name, $data = [])
    {
        if (isset($this->callbacks[$name])) {
            $this->_call($this->callbacks[$name])->ra($data)->perform();
        }
    }

    private function renderRowMap()
    {
        $columnsMap = $this->columnsMap;

        $rowMap = [];

        foreach ($columnsMap as $field => $columnChar) {
            $rowMap[] = $field . ' ' . (ord($columnChar) - 65);
        }

        $this->rowMap = implode(', ', $rowMap);
    }

    private function renderCheckColumnsNumbers()
    {
        $checkColumns = l2a($this->productCheckMap);

        $output = [];

        foreach ($checkColumns as $columnName) {
            if ($columnChar = $this->columnsMap[$columnName] ?? false) {
                $output[] = ord($columnChar) - 65;
            }
        }

        $this->rowCheckColumnsNumbers = $output;
    }

    protected function isProduct($row)
    {
        $isRow = true;

        foreach ($this->rowCheckColumnsNumbers as $columnNumber) {
            if (!$isRow = $isRow && !empty($row[$columnNumber])) {
                break;
            }
        }

        return $isRow;
    }

    protected function isCat($row)
    {
        if ($this->catNameColumn) {
            return !empty($row[ord($this->catNameColumn) - 65]);
        }
    }

    protected function getCatName($row)
    {
        return $row[ord($this->catNameColumn) - 65];
    }

    protected function getSpreadsheet()
    {
        $ext = pathinfo($this->filePath, PATHINFO_EXTENSION);

        if ($ext == 'xlsx') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        if ($ext == 'xls') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        }

        if (isset($reader)) {
            $spreadsheet = $reader->load($this->filePath);

            return $spreadsheet;
        }
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

            $output[] = $cellValue;
        }

        return $output;
    }
}
