<?php
namespace MtHash\Controller\Dashboard;
use MtHash\Controller\AbstractController;
use MtHash\Model\Mining\HASHContract;

class ChartController extends AbstractController
{
    public function getTotalHashrateByAsset (string $asset)
    {
        $period     = $this->request->getQuery ('period', 'string', '1d');

        $hashrater  = new HASHContract();
        $dynamics   = $hashrater->calculateDynamicsForPeriod ($asset, $period);

        $this->webResponse($dynamics);
    }
}