<?php
namespace MtHash\Controller\Dashboard;
use MtHash\Controller\AbstractController;
use MtHash\Model\Dashboard\Dashboard;

class DashboardController extends AbstractController
{
    public function getOverviewStatistics()
    {
        $this->webResponse(
            (new Dashboard())->getStatistics()
        );
    }

    public function getBlockRewards(?string $user = null)
    {

    }

    public function getMyActiveContracts()
    {

    }

}