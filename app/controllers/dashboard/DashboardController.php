<?php
namespace MtHash\Controller\Dashboard;
use MtHash\Controller\AbstractController;
use MtHash\Model\Dashboard\Dashboard;

class DashboardController extends AbstractController
{
    public function getOverviewStatistics()
    {
        $assetId    = $this->getFilter()['asset_id'] ?? null;
        $this->webResponse(
            (new Dashboard())->getStatistics($assetId)
        );
    }


}