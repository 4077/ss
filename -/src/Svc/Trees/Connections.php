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

    public function getDescendants($tree, $instance = '')
    {
        return \ss\models\TreesConnection::where('instance', $instance)->where('source_id', $tree->id)->get();
    }

    public function getAscendants($tree, $instance = '')
    {
        return \ss\models\TreesConnection::where('instance', $instance)->where('target_id', $tree->id)->get();
    }

    public function adapterData($connection, $adapter, $direction, $value = null)
    {
        $connectionData = _j($connection->data);

        if (null === $value) {
//            return (array)ap($connectionData, 'adapters/' . $adapter . '/' . $direction);
            return ap($connectionData, 'adapters/' . $adapter . '/' . $direction);
        } else {
            ap($connectionData, 'adapters/' . $adapter . '/' . $direction, $value);

            $connection->data = j_($connectionData);
            $connection->save();
        }
    }
}
