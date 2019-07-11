<?php
namespace MtHash\Model\User;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Mining\Block;
use Timestampable;

/**
 * Class Asset
 * @package MtHash\Model\User
 * @property User $user
 * @property Asset $asset
 */
class Asset extends AbstractEntity
{
    use Timestampable;

    public $id, $user_id, $asset_id, $hashrate, $shares;

    public function initialize()
    {
        $this->setSource ('user_asset');
        $this->belongsTo ('user_id', User::class, 'id', ['alias' => 'user']);
        $this->belongsTo ('asset_id', \MtHash\Model\Asset\Asset::class, 'id', ['alias' => 'asset']);
        $this->belongsTo ('block_id', Block::class, 'id', ['alias' => 'block']);
    }

}