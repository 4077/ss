<?php namespace ss\multisource\ui\division\importersControls\detectorMap\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    private function triggerUpdate()
    {
        $this->c('~:reload', [], 'importer');
    }

    public function create()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $map = _j($importer->detect_map) ?? [];

            $coords = array_keys($map);

            $lastCoord = end($coords);

            if ($lastCoord) {
                if (preg_match('/(\S+)(\d+)/', $lastCoord, $match)) {
                    $column = $match[1];
                    $row = $match[2];

                    $ord = ord($column);

                    do {
                        $newCoord = chr($ord++) . $row;
                    } while (isset($map[$newCoord]));

                    $map[$newCoord] = '';
                } else {
                    $map[] = '';
                }
            } else {
                $map['A1'] = '';
            }

            $importer->detect_map = j_($map);
            $importer->save();

            $this->triggerUpdate();
        }
    }

    public function delete()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $map = _j($importer->detect_map);
            $coord = $this->data('coord');

            if (isset($map[$coord])) {
                unset($map[$coord]);
            }

            $importer->detect_map = j_($map);
            $importer->save();

            $this->triggerUpdate();
        }
    }

    public function updateCoord()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $coord = $this->data('coord');

            $txt = \std\ui\Txt::value($this);

            $newCoord = strtoupper($txt->value);

            $map = _j($importer->detect_map);

            if (isset($map[$newCoord])) {
                $txt->response($coord);
            } else {
                $sequence = a2l(array_keys($map));
                $newSequence = str_replace($coord, $newCoord, $sequence);

                $map[$newCoord] = $map[$coord];

                unset($map[$coord]);

                $map = map($map, $newSequence);

                $importer->detect_map = j_($map);
                $importer->save();

                $this->triggerUpdate();
            }
        }
    }

    public function updateValue()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $coord = $this->data('coord');

            $txt = \std\ui\Txt::value($this);

            $newValue = $txt->value;

            $map = _j($importer->detect_map);

            $map[$coord] = $newValue;

            $importer->detect_map = j_($map);
            $importer->save();

            $valueSvc = \ss\multisource\ui\division\importersControls\detectorMap\valueSvc();

            list($content, $contentOnInit) = $valueSvc->getContent($newValue);

            $txt->response($content, $contentOnInit);

            $this->jquery($this->_selector('~:|') . " .row[coord='" . $coord . "'] > .value > .txt")
                ->removeClass("require_empty require_not_empty")
                ->addClass($valueSvc->getClass($newValue));
        }
    }

    public function arrange()
    {
        if ($importer = $this->unxpackModel('importer')) {
            $map = _j($importer->detect_map);

            $map = map($map, $this->data('sequence'));

            $importer->detect_map = j_($map);
            $importer->save();
        }
    }
}
