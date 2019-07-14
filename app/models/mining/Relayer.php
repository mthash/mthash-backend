<?php
namespace MtHash\Model\Mining;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\AssetRepository;
use MtHash\Model\User\User;
use Phalcon\Mvc\Model\ResultsetInterface;


class Relayer extends AbstractEntity
{
    use \Timestampable;

    public $id, $block_id, $user_id, $asset_id, $hashrate;

    public function initialize()
    {
        parent::initialize();
        $this->setSource ('relayer');
        $this->belongsTo ('user_id', User::class, 'id', ['alias' => 'user']);
        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
    }

    /**
     * @param User $user
     * @param Asset $asset
     * @return \Phalcon\Mvc\Model|self[]|ResultsetInterface
     */
    static public function byUser (User $user, Asset $asset) : ResultsetInterface
    {
        return self::find (
            [
                'status > 0 and user_id = ?0 and asset_id = ?1 and block_id >= ?2',
                'bind' => [$user->id, $asset->id, $asset->last_block_id - 1],
            ]
        );
    }

    public function increaseHashrate (int $hashrate) : void
    {
        $this->hashrate += $hashrate;
        $this->save();
    }

    public function decreaseHashrate (int $hashrate) : void
    {
        $this->hashrate -= $hashrate;
        $this->save();
    }

    static public function recalculateForAsset (Asset $asset) : void
    {
        $investors  = AssetRepository::getInvestorsInvestment($asset);

        foreach ($investors as $investor)
        {
            $hashrate   = $investor->hash_invested * $asset->total_hashrate / $asset->hash_invested;

            $relayer    = new self;
            $relayer->hashrate  = $hashrate;
            $relayer->user_id   = $investor->user_id;
            $relayer->asset_id  = $asset->id;
            $relayer->block_id  = $asset->last_block_id;
            $relayer->save();
        }
    }









}