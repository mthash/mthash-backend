<?php
namespace MtHash\Model\Mining\Pool;

use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Block;
use MtHash\Model\Mining\Pool\Miner\Miner;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Class Pool
 * @package MtHash\Model\Mining\Pool
 * @property Miner|ResultsetInterface $miners
 */

class Pool
{
    public $id, $name, $asset_id, $miners_count, $total_hashrate;

    use \Timestampable;

    public function initialize()
    {
        $this->setSource ('mining_pool');
        $this->hasMany ('id', Miner::class, 'pool_id', ['alias' => 'miners']);
    }

    public function mine (Asset $asset)
    {
        $block      = new Block();
        $block->generate (Miner::findFirst (1), $asset);
    }

    public function addMiner (array $minerData) : Pool
    {
        $minerData['pool_id']   = $this->id;
        $miner  = new Miner();
        $miner->save ($minerData);

        $this->miners_count++;
        $this->total_hashrate+= $miner->maxHashrate;
        $this->save();

        return $this;
    }

    public function removeMiner (Miner $miner) : Pool
    {
        $miner->delete();

        $this->miners_count--;
        $this->total_hashrate-= $miner->maxHashrate;

        $this->save();

        return $this;
    }



}