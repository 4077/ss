<?php namespace ss\controllers\main;

class Events extends \Controller
{
    public $singleton = true;

    private $events;

    public function __create()
    {
        $this->events = &$this->d(':events');
    }

    public function bind($path, $data)
    {
        ap($this->events, $path, $data);
    }

    public function trigger($path = false, $data = [])
    {
        $path or $path = $this->data('path');
        $data or $data = $this->data('data');

        if ($event = ap($this->events, $path)) {
            $triggersOutput = [];

            $data = pack_models($data);

            if ($triggers = ap($event, 'triggers')) {
                foreach ($triggers as $triggerName => $triggerData) {
                    if ($triggerData['enabled'] && $triggerCall = $triggerData['call']) {
                        $triggersOutput[$triggerName] = $this->_call($this->_abs($triggerCall))->ra($data)->perform();
                    }
                }
            }

            if ($event['log']['enabled'] ?? false) {
                ss()->sessionLog->write($path, $data, $triggersOutput);
            }
        }
    }
}
