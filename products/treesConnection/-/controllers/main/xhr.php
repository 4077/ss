<?php namespace ss\products\treesConnection\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function _toggle()
    {
        $this->dmap('~|', 'source_id, target_id, direction');

        $dataInstance = path($this->_instance(), $this->data('source_id'), $this->data('target_id'), $this->data('direction'));

        $d = &$this->d('~|' . $dataInstance);

        invert($d[$this->data('field')]);

        $this->c('~:reload|');
    }

    public function toggle()
    {
        $this->dmap('~|', 'connection, adapter, direction');
        $this->unpackModels();

        $connection = $this->data('connection');
        $direction = $this->data('direction');
        $adapter = $this->data('adapter');

        $data = ss()->trees->connections->adapterData($connection, $adapter, $direction);

        invert($data[$this->data('field')]);

        ss()->trees->connections->adapterData($connection, $adapter, $direction, $data);

        $this->c('~:reload|');
    }
}
