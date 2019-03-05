<?php namespace ss\Svc\Multisource;

class DivisionsIntersections extends \ewma\Service\Service
{
    protected $services = ['svc'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    //
    //
    //

    private $intersections;

    public function getIntersections()
    {
        if (null === $this->intersections) {
            $intersectionsModels = \ss\multisource\models\DivisionsIntersection::all();

            $this->intersections = [];

            foreach ($intersectionsModels as $intersectionModel) {
                $this->intersections[$intersectionModel->source_id][$intersectionModel->target_id] = $intersectionModel;
            }
        }

        return $this->intersections;
    }

    public function getIntersection(\ss\multisource\models\Division $source, \ss\multisource\models\Division $target)
    {
        $intersections = $this->getIntersections();

        return $intersections[$source->id][$target->id] ?? null;
    }

    public function create(\ss\multisource\models\Division $source, \ss\multisource\models\Division $target)
    {
        return \ss\multisource\models\DivisionsIntersection::create([
                                                                        'source_id'         => $source->id,
                                                                        'target_id'         => $target->id,
                                                                        'price_coefficient' => 1
                                                                    ]);
    }

    public function getPriceUpdateMap(\ss\multisource\models\Division $sourceDivision)
    {
        $intersections = $this->getIntersections()[$sourceDivision->id] ?? [];

        $output = [];

        foreach ($intersections as $targetDivisionId => $intersection) {
            $output[$targetDivisionId] = $intersection->price_coefficient;
        }

        return $output;
    }
}
