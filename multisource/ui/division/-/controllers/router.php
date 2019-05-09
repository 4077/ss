<?php namespace ss\multisource\ui\division\controllers;

class Router extends \Controller implements \ewma\Interfaces\RouterInterface
{
    public function getResponse()
    {
        $this->route('last')->to(':lastDivisionView');
        $this->route('{division_id}')->to(':divisionView');

        return $this->routeResponse();
    }

    public function lastDivisionView()
    {
        $lastDivisionId = $this->s(':last_division_id');

        if (!$lastDivisionId) {
            if ($lasCreatedDivision = \ss\multisource\models\Division::orderBy('id', 'DESC')->first()) {
                $lastDivisionId = $lasCreatedDivision->id;
            }
        }

        $this->data('division_id', $lastDivisionId);

        return $this->divisionView();
    }

    public function divisionView()
    {
        if ($division = \ss\multisource\models\Division::find($this->data('division_id'))) {
            $this->s(':last_division_id', $division->id, RR);

            return $this->c('~:view', [
                'division' => $division
            ]);
        }
    }
}
