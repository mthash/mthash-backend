<?php
namespace MtHash\Model\Mining\Pool\Miner;
use MtHash\Model\AbstractModel;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Block;

class Miner extends AbstractModel
{
    public $id, $pool_id, $algo_id, $max_hashrate;

    public function mine (Asset $asset)
    {
        $block      = new Block();
        $block->generate ($this, $asset);
    }
}