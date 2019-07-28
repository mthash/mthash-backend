<?php
namespace MtHash\Model\Mining;
use MtHash\Model\AbstractModel;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Pool\Miner\Miner;
use MtHash\Model\User\User;

/**
 * Class Block
 * @package MtHash\Model\Mining
 * @property User $user
 * @property \Pool $pool
 * @property Asset $asset
 */
class Block extends AbstractModel
{
    public $id, $asset_id, $miner_id, $pool_id, $hash, $reward;

    use \Timestampable;

    public function initialize()
    {
        $this->setSource ('block');
        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
    }

    public function generate(Miner $miner, Asset $asset) : Block
    {
        $this->save (
            [
                'asset_id'          => $asset->id,
                'miner_id'          => $miner->id,
                'pool_id'           => $miner->pool_id,
                'hash'              => $this->getBlockHash(),
                'reward'            => $this->getBlockReward($asset),
            ]
        );

        return $this;
    }

    private function getBlockReward (Asset $asset)
    {
        return $asset->block_reward_amount;
    }

    private function getBlockHash()
    {
        return hash ('SHA256', microtime(true));
    }

    static public function getRewardsWizard() : array
    {
        $blocks = self::find (
            [
                'status > 0',
                'limit'         => 20,
                'order'         => 'id DESC',
            ]
        );

        $response   = [];
        foreach ($blocks as $block)
        {
            $response[] = (new BlockDTO($block))->fetch();
        }

        return $response;
    }

}