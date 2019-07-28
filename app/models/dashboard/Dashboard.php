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
        $todayRevenue   = \Phalcon\Di::getDefault()->get('db')->query ('
            SELECT `currency`, SUM(`amount`) as `amount`, (SELECT `price_usd` FROM `asset` WHERE `symbol` = `currency`) as `price_usd`
            FROM `transaction`
            WHERE `type_id` = 2 AND `from_user_id` = -1 and (`created_at` >= ' . strtotime ('today 00:00:00') . ' AND `created_at` <= ' . strtotime ('today 23:59:59') . ')
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