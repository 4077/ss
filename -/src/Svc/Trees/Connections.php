<?php namespace ss\Svc\Trees;

class Connections extends \ewma\Service\Service
{
    public function get($treeA, $treeB, $instance = '')
    {
        return $this->getByIds($treeA->id, $treeB->id, $instance);
    }

    public function getByIds($treeAId, $treeBId, $instance = '')
    {
        return \ss\models\TreesConnection::where('instance', $instance)->where(function ($query) use ($treeAId, $treeBId) {
            $query->where('source_id', $treeAId)->where('target_id', $treeBId);
        })->orWhere(function ($query) use ($treeAId, $treeBId) {
            $query->where('source_id', $treeBId)->where('target_id', $treeAId);
        })->first();
    }

    private $descendants;

    public function getDescendants($tree, $instance = '')
    {
        if (!isset($this->descendants[$tree->id][$instance])) {
            $this->descendants[$tree->id][$instance] = \ss\models\TreesConnection::where('instance', $instance)->where('source_id', $tree->id)->get();
        }

        return $this->descendants[$tree->id][$instance];
    }

    private $ascendants;

    public function getAscendants($tree, $instance = '')
    {
        if (!isset($this->ascendants[$tree->id][$instance])) {
            $this->ascendants[$tree->id][$instance] = \ss\models\TreesConnection::where('instance', $instance)->where('target_id', $tree->id)->get();
        }

        return $this->ascendants[$tree->id][$instance];
    }

    private $adaptersData;

    public function adapterData($connection, $adapter, $direction, $value = null)
    {
        $connectionData = _j($connection->data);

        if (null === $value) {
            if (!isset($this->adaptersData[$connection->id][$adapter][$direction])) {
                $this->adaptersData[$connection->id][$adapter][$direction] = ap($connectionData, 'adapters/' . $adapter . '/' . $direction);
            }

            return $this->adaptersData[$connection->id][$adapter][$direction];
        } else {
            ap($connectionData, 'adapters/' . $adapter . '/' . $direction, $value);

            $connection->data = j_($connectionData);
            $connection->save();
        }
    }
}
