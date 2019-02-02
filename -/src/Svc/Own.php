<?php namespace ss\Svc;

class Own extends \ewma\Service\Service
{
    public function isCatOwn($treeId, $catOrCatId)
    {
        if ($user = ss()->access->getUser()) {
            if (appc()->isSuperuser()) {
                return true;
            } else {
                if ($catOrCatId instanceof \ss\models\Cat) {
                    $catId = $catOrCatId->id;
                }

                if (is_numeric($catOrCatId)) {
                    $catId = $catOrCatId;
                }

                if (isset($catId)) {
                    $ownCatsIds = $this->getOwnCatsIds($treeId, $user->model);

                    return in_array($catId, $ownCatsIds);
                }
            }
        } else {
            return false;
        }
    }

    private $ownCatsIds;

    public function getOwnCatsIds($treeId, \ss\models\User $user)
    {
        if (!isset($this->ownCatsIds[$treeId])) {
            $treeInfo = $this->getTreeInfo($treeId, $user);

            $this->ownCatsIds[$treeId] = [];

            merge($this->ownCatsIds[$treeId], $treeInfo->enabledIds);
            merge($this->ownCatsIds[$treeId], $treeInfo->autoEnabledIds);
        }

        return $this->ownCatsIds[$treeId];
    }

    private $treeInfo;

    public function getTreeInfo($treeId, \ss\models\User $user)
    {
        $cacheIndex = $treeId . '/' . $user->id;

        if (!isset($this->treeInfo[$cacheIndex])) {
            $this->treeInfo[$cacheIndex] = (new \ss\Svc\Own\TreeInfo)->render($treeId, $user);
        }

        return $this->treeInfo[$cacheIndex];
    }

    public function toggleUserCatLink(\ss\models\User $user, \ss\models\Cat $cat, $mode)
    {
        $link = $user->cats->find($cat);

        if ($link) {
            $currentMode = $link->pivot->mode;

            if ($currentMode == strtoupper($mode)) {
                $user->cats()->detach([$cat->id]);
            } else {
                $user->cats()->detach([$cat->id]);
                $user->cats()->attach([$cat->id], ['mode' => strtoupper($mode)]);
            }
        } else {
            $user->cats()->detach([$cat->id]);
            $user->cats()->attach([$cat->id], ['mode' => strtoupper($mode)]);
        }
    }
}
