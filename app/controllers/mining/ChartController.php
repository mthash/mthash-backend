<?php
namespace MtHash\Controller\Mining;
use MtHash\Controller\AbstractController;
use MtHash\Model\Asset\Algo;
use MtHash\Model\Historical\AbstractHistorical;
use MtHash\Model\Historical\Arcade;
use MtHash\Model\Historical\DailyRevenue;
use MtHash\Model\Mining\Pool\Pool;

class ChartController extends AbstractController
{
    private function possibleChartTypes() : array
    {
        return ['pools', 'algorithms', 'tokens', 'power', 'daily_revenue'];
    }

    private function possiblePeriodLiterals() : array
    {
        return ['1h', '3h', '1d', '7d', '1m', 'all'];
    }

    public function getChart (?string $type = null)
    {
        $period     = $this->getFilter()['period'] ?? null;
        $assetId    = $this->getFilter()['asset_id'] ?? null;

        if ($period == 'all') $period = null;

        $this->validateInput(
            [
                'type'          => $type,
                'asset_id'      => $assetId,
                'period'        => $period,
            ],
            [
                'type'          => ['optional', ['in', $this->possibleChartTypes()]],
                'asset_id'      => ['optional', 'numeric'],
                'period'        => ['alphaNum', ['in', $this->possiblePeriodLiterals()]]
            ]
        );

        $data   = [];

        switch ($type)
        {
            case 'pools':
                $data   = Pool::generateChart($period, $assetId);
            break;

            case 'algorithms':
                $data   = Algo::generateChart($period, $assetId);
            break;

            case 'tokens':
                $data   = Arcade::generateChart($period, $assetId);
            break;

            case 'power':
                $data   = Pool::generatePowerConsumptionChart($period);
            break;

            case 'daily_revenue':
                $data   = DailyRevenue::generateChart($period, $assetId);
            break;

            default:
                $data['pools']          = Pool::generateChart($period);
                $data['algorithms']     = Algo::generateChart($period);
                $data['tokens']         = Arcade::generateChart($period, $assetId);
                $data['power']          = Pool::generatePowerConsumptionChart($period);
                $data['daily_revenue']  = DailyRevenue::generateChart($period, $assetId);
        }

        $this->webResponse($data);
    }
}