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

    static public function calculateRevenue (User $user, \MtHash\Model\Asset\Asset $asset) : float
    {
        return \Phalcon\Di::getDefault()->get('db')->query ('
            SELECT SUM(`amount`) / 24 as `revenue`
            FROM `transaction`
            WHERE `type_id` = 2 AND `from_user_id` = -1 and (`created_at` >= ' . strtotime ('today 00:00:00') . ' AND `created_at` <= ' . strtotime ('today 23:59:59') . ')
            AND `to_user_id` = ' . $user->id . ' AND `currency` = "' . $asset->symbol . '"
        ')->fetch (\PDO::FETCH_COLUMN);
    }

}