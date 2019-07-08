<?php
namespace MtHash\Model\Asset;
use MtHash\Model\AbstractEntity;

class Asset extends AbstractEntity
{
    private $_assets    = ['ETH', 'BTC', 'LTC', 'BCH', 'ADA', 'TRX', 'XMR', 'DASH', 'ETC'];

    public $id, $cmc_id, $logo_url, $symbol, $name, $mineable, $can_mine, $total_hashrate, $hash_invested, $price_usd, $block_generation_time, $block_reward_amount;

    static public function calculateExchangeRate (Asset $firstAsset, Asset $secondAsset) : float
    {
        return $secondAsset->price_usd / $firstAsset->price_usd;
    }



}