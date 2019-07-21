<?php
namespace MtHash\Model\Dashboard;
use MtHash\Model\Mining\Pool\Pool;
use MtHash\Model\Asset\Asset;

class Dashboard
{
    private function getPoolCount() : int
    {
        return Pool::count();
    }

    private function getAlgorithmsCount() : int
    {
        return Asset::count();
    }

    private function getTokensCount() : int
    {
        return Asset::sum (['column' => 'hash_invested']);
    }

    private function getPower() : array
    {
        return
        [
            'value'         => 93,
            'unit'          => 'MW',
        ];
    }

    private function getDailyRevenue() : array
    {
        return
        [
            'value'         => 2.3,
            'unit'          => 'M',
        ];
    }


    public function getStatistics() : array
    {
        return
        [
            'pools'                 => $this->getPoolCount(),
            'algorithms'            => $this->getAlgorithmsCount(),
            'tokens'                => $this->getTokensCount(),
            'power'                 => $this->getPower(),
            'daily_revenue'         => $this->getDailyRevenue(),
        ];
    }



}