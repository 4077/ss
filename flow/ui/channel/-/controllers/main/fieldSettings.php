<?php namespace ss\flow\ui\channel\controllers\main;

class FieldSettings extends \Controller
{
    private $channel;

    private $channelXPack;

    private $type;

    private $settings;

    public function __create()
    {
        if ($this->channel = $this->unpackModel('channel')) {
            $this->channelXPack = xpack_model($this->channel);

            $this->settings = _j($this->channel->settings);

            $this->type = $this->data('type');

            $this->instance_($this->channel->id . '/' . $this->type);
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

        $v->assign([
                       'FIELD_SELECTOR'             => $this->fieldSelectorView(),
                       'PREPROCESSOR_ENABLED_CLASS' => ap($this->settings, 'collation/field_preprocessor_enabled/' . $this->type) ? 'enabled' : '',
                       'PREPROCESSOR_TOGGLE_BUTTON' => $this->preprocessorToggleButton(),
                       'PREPROCESSOR_EDIT_BUTTON'   => $this->preprocessorEditButton()
                   ]);

        $this->css();

        return $v;
    }

    private function preprocessorToggleButton()
    {
        return $this->c('\std\ui button:view', [
            'path'  => '~xhr:toggleFieldPreprocessor',
            'data'  => [
                'channel' => $this->channelXPack,
                'type'    => $this->type
            ],
            'class' => 'toggle_button ',
            'icon'  => 'fa fa-power-off'
        ]);
    }

    private function preprocessorEditButton()
    {
        return $this->c('\std\ui button:view', [
            'path'    => '~xhr:editFieldPreprocessor',
            'data'    => [
                'channel' => $this->channelXPack,
                'type'    => $this->type
            ],
            'class'   => 'edit_button',
            'content' => 'предобработка'
        ]);
    }

    private function fieldSelectorView()
    {
        return $this->c('\std\ui select:view', [
            'path'     => '~xhr:selectCollationField',
            'data'     => [
                'channel' => $this->channelXPack,
                'type'    => $this->type
            ],
            'items'    => [
                'articul'           => 'Артикул',
                'remote_articul'    => 'Артикул (ориг.)',
                'vendor_code'       => 'Код производителя',
                'name'              => 'Наименование',
                'short_name'        => 'Короткое наименование',
                'remote_name'       => 'Наименование (ориг.)',
                'remote_short_name' => 'Короткое наименование (ориг.)',
            ],
            'selected' => ap($this->settings, 'collation/field/' . $this->type),
            'class'    => 'field'
        ]);
    }
}
