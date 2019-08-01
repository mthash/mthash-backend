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

    const DEFAULT_ASSET = 'BTC';

    static public function calculateExchangeRate (Asset $firstAsset, Asset $secondAsset) : float
    {
        return $firstAsset->price_usd / $secondAsset->price_usd;
    }

    public function generateBlock(Miner $miner) : Block
    {
        $block      = new Block();
        $block->generate($miner, $this);

        $this->last_block_id    = $block->id;
        $this->save();

        return $block;
    }

    public function getPreviousBlockId() : int
    {
        $blocks = Block::find (
            [
                'status > 0 and asset_id = ?0', 'bind' => [$this->id],
                'order' => 'id DESC',
                'limit' => 2,
            ]
        );

        if ($blocks && $blocks->count() > 1)
        {
            return $blocks[1]->id;
        }
        elseif ($blocks && $blocks->count() == 1)
        {
            return $blocks[0]->id;
        }
        else
        {
            return 0;
        }
    }

    public function getUsingHashrate() : int
    {
        $usingHashrate  = $this->getDI()->get('db')->query ('
            SELECT SUM(`hashrate`) FROM `relayer` WHERE `asset_id` = ' . $this->id . ' AND `block_id` >= ' . $this->last_block_id . '
        ')->fetch (\PDO::FETCH_COLUMN);

        return (int) $usingHashrate;
    }



}