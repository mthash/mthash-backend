<?php
namespace MtHash\Model\Mining\Pool;

use MtHash\Model\AbstractModel;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Block;
use MtHash\Model\Mining\Pool\Miner\Miner;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Class Pool
 * @package MtHash\Model\Mining\Pool
 * @property Miner|ResultsetInterface $miners
 * @property Asset $asset
 */

class Pool extends AbstractModel
{
    public $id, $name, $asset_id, $miners_count, $total_hashrate;

    use \Timestampable;

    public function initialize()
    {
        $this->setSource ('mining_pool');
        $this->hasMany ('id', Miner::class, 'pool_id', ['alias' => 'miners']);
        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
    }

    public function mine (Asset $asset)
    {
        $block      = new Block();
        $block->generate (Miner::findFirst (1), $asset);

        $asset->last_block_id   = $block->id;
        $asset->total_hashrate  = $this->total_hashrate; // @todo Change this when we will have multiple pools
        $asset->save();

        return $block;
    }

    public function addMiner (array $minerData) : Pool
    {
        $minerData['pool_id']   = $this->id;
        $miner  = new Miner();
        $miner->save ($minerData);

        $this->miners_count++;
        $this->total_hashrate+= $miner->max_hashrate;
        $this->save();

        return $this;
    }

    public function removeMiner (Miner $miner) : Pool
    {
        $miner->delete();

        $this->miners_count--;
        $this->total_hashrate-= $miner->max_hashrate;

        $this->save();

        return $this;
    }



}