<?php namespace ss\Svc;

class Multisource extends \ewma\Service\Service
{
    protected $services = ['svc', 'divisionsIntersections'];

    /**
     * @var \ss\Svc
     */
    public $svc = \ss\Svc::class;

    /**
     * @var \ss\Svc\Multisource\DivisionsIntersections
     */
    public $divisionsIntersections = \ss\Svc\Multisource\DivisionsIntersections::class;

    //
    //
    //

    private $divisions;

    public function getDivisions()
    {
        if (null === $this->divisions) {
            $this->divisions = table_rows_by_id(\ss\multisource\models\Division::orderBy('position')->get());
        }

        return $this->divisions;
    }

    public function getDivision($divisionId)
    {
        $divisions = $this->getDivisions();

        return $divisions[$divisionId] ?? null;
    }

    private $warehouses;

    public function getWarehouses()
    {
        if (null === $this->warehouses) {
            $this->warehouses = table_rows_by_id(\ss\multisource\models\Warehouse::orderBy('position')->get());
        }

        return $this->warehouses;
    }

    public function getWarehouse($warehouseId)
    {
        $warehouses = $this->getWarehouses();

        return $warehouses[$warehouseId] ?? null;
    }

    private $warehousesGroups;

    public function getWarehousesGroups()
    {
        if (null === $this->warehousesGroups) {
            $this->warehousesGroups = table_rows_by_id(\ss\multisource\models\WarehouseGroup::orderBy('position')->get());
        }

        return $this->warehousesGroups;
    }

    public function updateSummary(\ss\models\Product $product)
    {
        $groups = $this->getWarehousesGroups();

        foreach ($groups as $group) {
            $this->updateGroupSummary($product, $group);
        }
    }

    private $groupsWarehouses = [];

    private function getGroupWarehouses(\ss\multisource\models\WarehouseGroup $group)
    {
        if (!isset($this->groupsWarehouses[$group->id])) {
            $this->groupsWarehouses[$group->id] = $group->warehouses()->get();
        }

        return $this->groupsWarehouses[$group->id];
    }

    public function updateGroupSummary(\ss\models\Product $product, \ss\multisource\models\WarehouseGroup $group)
    {
        $summary = $this->getGroupSummary($product, $group);

        $warehouses = $this->getGroupWarehouses($group);

        $divisionsIds = [];
        $warehousesIds = [];

        foreach ($warehouses as $warehouse) {
            merge($divisionsIds, $warehouse->division_id);
            merge($warehousesIds, $warehouse->id);
        }

        $productOnDivisions = \ss\multisource\models\ProductDivision::where('product_id', $product->id)
            ->whereIn('division_id', $divisionsIds)
            ->get();

        $minPrice = PHP_INT_MAX;
        $maxPrice = 0;
        $minDiscount = PHP_INT_MAX;
        $maxDiscount = 0;

        foreach ($productOnDivisions as $productOnDivision) {
            $minPrice = min($minPrice, $productOnDivision->price);
            $maxPrice = max($maxPrice, $productOnDivision->price);

            $minDiscount = min($minDiscount, $productOnDivision->discount);
            $maxDiscount = max($maxDiscount, $productOnDivision->discount);
        }

        $productOnWarehouses = \ss\multisource\models\ProductWarehouse::where('product_id', $product->id)
            ->whereIn('warehouse_id', $warehousesIds)
            ->get();

        $stock = 0;
        $reserved = 0;

        foreach ($productOnWarehouses as $productOnWarehouse) {
            $stock += $productOnWarehouse->stock;
            $reserved += $productOnWarehouse->reserved;
        }

        $summary->stock = $stock;
        $summary->reserved = $reserved;

        $summary->min_price = $minPrice == PHP_INT_MAX ? 0 : $minPrice;
        $summary->max_price = $maxPrice;
        $summary->min_discount = $minDiscount == PHP_INT_MAX ? 0 : $minDiscount;
        $summary->max_discount = $maxDiscount;

        $summary->save();

        return $summary;
    }

    private $multisourceSummaryCache;

    public function getSummary(\ss\models\Product $product)
    {
        $groups = $this->getWarehousesGroups();

        $output = [];

        foreach ($groups as $group) {
            $output[$group->id] = $this->getGroupSummary($product, $group);
        }

        return $output;
    }

    public function getGroupSummary(\ss\models\Product $product, \ss\multisource\models\WarehouseGroup $group)
    {
        if (!isset($this->multisourceSummaryCache[$group->id][$product->id])) {
            if (!$summary = $product->multisourceSummary()->where('warehouses_group_id', $group->id)->first()) {
                $summary = $product->multisourceSummary()->create(['warehouses_group_id' => $group->id]);
            }

            $this->multisourceSummaryCache[$group->id][$product->id] = $summary;
        }

        return $this->multisourceSummaryCache[$group->id][$product->id];
    }

    public function getDivisionByWarehouse(\ss\multisource\models\Warehouse $warehouse)
    {
        $divisionsByWarehouses = $this->getDivisionsByWarehouses();

        return $divisionsByWarehouses[$warehouse->id] ?? null;
    }

    private $divisionsIdsByWarehousesIds;

    public function getDivisionIdByWarehouseId($warehouseId)
    {
        if (null === $this->divisionsIdsByWarehousesIds) {
            $divisionsByWarehouses = $this->getDivisionsByWarehouses();

            foreach ($divisionsByWarehouses as $wid => $division) {
                $this->divisionsIdsByWarehousesIds[$wid] = $division->id;
            }
        }

        return $this->divisionsIdsByWarehousesIds[$warehouseId] ?? false;
    }

    private $divisionsByWarehouses;

    public function getDivisionsByWarehouses()
    {
        if (null === $this->divisionsByWarehouses) {
            $warehouses = $this->getWarehouses();
            $divisions = $this->getDivisions();

            foreach ($warehouses as $warehouse) {
                $this->divisionsByWarehouses[$warehouse->id] = $divisions[$warehouse->division_id];
            }
        }

        return $this->divisionsByWarehouses;
    }
}
