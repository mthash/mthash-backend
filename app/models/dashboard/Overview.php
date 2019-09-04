<?php
namespace MtHash\Model\Dashboard;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Pool\Pool;

/**
 * Class Overview
 * @package MtHash\Model\Dashboard
 * @property Asset $asset
 */
class Overview extends AbstractEntity
{
    public $id, $asset_id, $daily_revenue;

    public function initialize()
    {
        parent::initialize();
        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
    }

    public function getStaticDailyRevenuePerAsset() : array
    {
        $assets = Asset::find ('status > 0 and mineable = 1');
        $return = [];

        foreach ($assets as $asset)
        {
            $return[] = [
                'id'        => $asset->id,
                'symbol'    => $asset->symbol,
                'revenue'   => Overview::failFindFirst(['asset_id = ?0', 'bind' => [$asset->id]])->daily_revenue,
            ];
        }

        return $return;
    }

    public function getPowerPerPool() : array
    {
        $return = [];
        foreach (Pool::find() as $pool)
        {
            $return[] = ['id' => $pool->id, 'name' => $pool->name, 'power' => $pool->used_power, 'hashrate' => $pool->total_hashrate];
        }

        return $return;
    }

    public function getStaticData() : array
    {
        return
            [
                'daily_revenue'     => $this->getStaticDailyRevenuePerAsset(),
                'power'             => $this->getPowerPerPool(),
            ];
    }

    public function updateDailyRevenue (array $data)
    {
        foreach ($data as $assetId => $revenue)
        {
            $asset  = self::findFirst (
                [
                    'asset_id = ?0', 'bind' => [$assetId]
                ]
            );

            if ($asset && $asset->count() > 0)
            {
                $asset->save (['daily_revenue' => $revenue]);
            }
        }

        return true;
    }

    public function updatePool (array $data)
    {
        foreach ($data as $poolId => $data)
        {
            $pool   = Pool::findFirst ($poolId);
            if ($pool && $pool->count() > 0)
            {
                $pool->save (
                    [
                        'power'     => $data['power'],
                        'hashrate'  => $data['hashrate'],
                    ]
                );
            }
        }

        return true;
    }

}