<?php
namespace MtHash\Model\User;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Entity;
use MtHash\Model\Mining\Block;
use Timestampable;

/**
 * Class Asset
 * @package MtHash\Model\User
 * @property User $user
 * @property \MtHash\Model\Asset\Asset $asset
 */
class Asset extends AbstractEntity
{
    use Timestampable;

    public $id, $user_id, $asset_id, $is_visible;

    public function initialize()
    {
        $this->setSource ('user_asset');
        $this->belongsTo ('user_id', User::class, 'id', ['alias' => 'user']);
        $this->belongsTo ('asset_id', \MtHash\Model\Asset\Asset::class, 'id', ['alias' => 'asset']);
        $this->belongsTo ('block_id', Block::class, 'id', ['alias' => 'block']);
    }

    static public function calculateRevenue (User $user, \MtHash\Model\Asset\Asset $asset) : ?float
    {
        return \Phalcon\Di::getDefault()->get('db')->query ('
            SELECT SUM(`amount`) AS `revenue`
            FROM `transaction`
            WHERE `type_id` = 2 AND `from_user_id` = -1 and `created_at` > ' . strtotime ('-1 hour') . '
            AND `to_user_id` = ' . $user->id . ' AND `currency` = "' . $asset->symbol . '"
        ')->fetch (\PDO::FETCH_COLUMN);
    }

    public function makeVisible()
    {
        $this->is_visible   = 1;
        $this->save();
    }

    public function makeInvisible()
    {
        $this->is_visible   = 0;
        $this->save();
    }

    public function toggleVisibility()
    {
        $this->is_visible   ^= 1;
        $this->save();
    }

}