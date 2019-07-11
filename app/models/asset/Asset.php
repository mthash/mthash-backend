<?php
namespace MtHash\Model\Asset;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Mining\Block;
use MtHash\Model\Mining\Pool\Miner\Miner;

class Asset extends AbstractEntity
{
    private $_assets    = ['ETH', 'BTC', 'LTC', 'BCH', 'ADA', 'TRX', 'XMR', 'DASH', 'ETC'];

    public $id, $cmc_id, $logo_url, $symbol, $name, $mineable, $can_mine, $total_hashrate, $hash_invested, $price_usd, $block_generation_time, $block_reward_amount,
    $shares, $last_block_id;

    static public function calculateExchangeRate (Asset $firstAsset, Asset $secondAsset) : float
    {
        return $secondAsset->price_usd / $firstAsset->price_usd;
    }

    public function generateBlock(Miner $miner) : Block
    {
        $block      = new Block();
        $block->generate($miner, $this);

        $this->last_block_id    = $block->id;
        $this->save();

        return $block;
    }



}