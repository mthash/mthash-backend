<?php
namespace MtHash\Model\Mining\Pool;

use MtHash\Model\Asset\Asset;

abstract class AbstractMiningPool
{
    public $asset_id, $miner_id, $pool_id, $hash, $reward;

    use \Timestampable;

    public function generateBlock(Asset $asset)
    {
        $this->save (
            [
                'asset_id'          => $asset->id,
                'pool_id'           => $this->id,
                'hash'              => hash ('sha256', microtime (true)),
                'reward'            => $asset->block_reward_amount,
            ]
        );

        return $this;
    }


}