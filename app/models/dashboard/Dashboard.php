<?php
namespace MtHash\Model\Dashboard;
use MtHash\Model\Mining\Pool\Pool;
use MtHash\Model\Asset\Asset;

class Dashboard
{
    private function getPoolCount(?int $assetId) : int
    {
        $request    = !empty ($assetId) ? 'id = ' . $assetId : '';
        return Asset::count($request);
    }

    private function getAlgorithmsCount(?int $assetId = null) : int
    {
        $request    = !empty ($assetId) ? 'id = ' . $assetId : '';
        return Asset::count($request);
    }

    private function getTokensCount(?int $assetId = null) : int
    {
        $request    = !empty ($assetId) ? 'id = ' . $assetId : '';
        return Asset::sum ([$request, 'column' => 'hash_invested']);
    }

    private function getPower(?int $assetId = null) : array
    {
        if (!empty ($assetId)) $asset  = Asset::failFindFirst($assetId);

        $power  = empty ($asset) ? \Phalcon\Di::getDefault()->get('db')->query ('SELECT SUM(`used_power`) FROM `mining_pool`')->fetch (\PDO::FETCH_COLUMN) : $asset->algo->pool->used_power;

        return
        [
            'value'         => $power / 1000000,
            'unit'          => 'MW',
        ];
    }

    private function getDailyRevenue(?int $assetId = null) : array
    {
        $request    = !empty ($assetId) ? ' AND currency = "' . Asset::failFindFirst($assetId)->symbol . '"': '';
        $todayRevenue   = \Phalcon\Di::getDefault()->get('db')->query ('
            SELECT `currency`, SUM(`amount`) as `amount`, (SELECT `price_usd` FROM `asset` WHERE `symbol` = `currency`) as `price_usd`
            FROM `transaction`
            WHERE `type_id` = 2 AND `from_user_id` = -1 and (`created_at` >= ' . strtotime ('today 00:00:00') . ' AND `created_at` <= ' . strtotime ('today 23:59:59') . ') ' . $request . '
            GROUP by `currency`
        ')->fetchAll (\PDO::FETCH_ASSOC);

        $revenue    = 0;
        foreach ($todayRevenue as $item)
        {
            $revenue+= $item['amount'] * $item['price_usd'];
        }


        $isMillion  = $revenue / 1000000 > 1;

        return
        [
            'raw'           => $revenue,
            'value'         => $isMillion ? round ($revenue / 1000000, 2) : round ($revenue / 1000, 2),
            'unit'          => $isMillion ? 'M' : 'K',
        ];
    }


    public function getStatistics(?int $assetId = null) : array
    {
        return
        [
            'pools'                 => $this->getPoolCount($assetId),
            'algorithms'            => $this->getAlgorithmsCount($assetId),
            'tokens'                => $this->getTokensCount($assetId),
            'power'                 => $this->getPower($assetId),
            'daily_revenue'         => $this->getDailyRevenue($assetId),
        ];
    }



}