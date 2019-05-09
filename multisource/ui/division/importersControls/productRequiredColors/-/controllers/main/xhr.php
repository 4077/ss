<?php namespace ss\multisource\ui\division\importersControls\productsRequiredColors\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private function reload()
    {
        $this->c('~:reload', [], 'importer');
    }

    private $defaultData = [
        'enabled' => false,
        'colors'  => [
            'bg' => 'FFFFFF',
            'fg' => '000000'
        ],
        'columns' => ''
    ];

    private function getImporter()
    {
        return $this->unxpackModel('importer');
    }

    private function getSettings($importer)
    {
        return _j($importer->product_required_colors);
    }

    private function setSettings($importer, $settings)
    {
        aa($settings, $this->defaultData);

        $importer->product_required_colors = j_($settings);
        $importer->save();
    }

    public function toggle()
    {
        if ($importer = $this->getImporter()) {
            $settings = $this->getSettings($importer);

            $enabled = &ap($settings, 'enabled');

            invert($enabled);

            $this->setSettings($importer, $settings);

            $this->reload();
        }
    }

    public function updateColor()
    {
        if ($importer = $this->getImporter()) {
            $settings = $this->getSettings($importer);

            $txt = \std\ui\Txt::value($this);

            $dec = hexdec($txt->value);

            if ($dec > 16777215) {
                $dec = 16777215;
            }

            $hex = dechex($dec);

            $hex = str_pad($hex, 6, '0', STR_PAD_LEFT);

            $value = strtoupper($hex);

            ap($settings, 'colors/' . $this->data('type'), $value);

            $this->setSettings($importer, $settings);

            $this->reload();
        }
    }

    public function updateColumns()
    {
        if ($importer = $this->getImporter()) {
            $settings = $this->getSettings($importer);

            $txt = \std\ui\Txt::value($this);

            ap($settings, 'columns', $txt->value);

            $this->setSettings($importer, $settings);

            $txt->response();
        }
    }
}
