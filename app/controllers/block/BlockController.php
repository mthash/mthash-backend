<?php
namespace MtHash\Controller\Block;
use MtHash\Controller\AbstractController;
use MtHash\Model\Mining\Block;

class BlockController extends AbstractController
{
    public function getRewardsWidget (?string $user = null)
    {
        $filter     = $this->getFilter();
        $rewards    = Block::getRewardsWidget($filter);
        return $this->webResponse($rewards);
    }

    public function getMyRewardsWidget ()
    {
        $rewards    = Block::myRewardsWidget($this->currentUser, $this->getFilter());
        return $this->webResponse($rewards);
    }
}