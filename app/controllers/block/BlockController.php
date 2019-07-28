<?php
namespace MtHash\Controller\Block;
use MtHash\Controller\AbstractController;
use MtHash\Model\Mining\Block;

class BlockController extends AbstractController
{
    public function getRewardsWidget (?string $user = null)
    {
        $rewards    = Block::getRewardsWizard();
        return $this->webResponse($rewards);
    }
}