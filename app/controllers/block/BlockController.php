<?php
namespace MtHash\Controller\Block;
use MtHash\Controller\AbstractController;
use MtHash\Model\Mining\Block;

class BlockController extends AbstractController
{
    public function getRewardsWidget (?string $user = null)
    {
        $rewards    = Block::getRewardsWidget();
        return $this->webResponse($rewards);
    }

    public function getMyRewardsWidget ()
    {
        $rewards    = Block::myRewardsWidget($this->currentUser);
        return $this->webResponse($rewards);
    }
}