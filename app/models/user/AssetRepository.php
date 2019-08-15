<?php
namespace MtHash\Model\User;
class AssetRepository
{
    static public function find (User $user, \MtHash\Model\Asset\Asset $asset) : Asset
    {
        $record = Asset::findFirst (
            [
                'status > 0 and user_id = ?0 and asset_id = ?1',
                'bind' => [$user->id, $asset->id]
            ]
        );

        if (!$record)
        {
            $record = new Asset();
            $record->asset_id   = $asset->id;
            $record->user_id    = $user->id;
            $record->is_visible = 1;
            $record->save();
        }

        return $record;
    }

    /**
     * @param User $user
     * @return \MtHash\Model\AbstractModel[]|Asset[]|\Phalcon\Mvc\Model\ResultsetInterface|void
     */
    static public function allVisible (User $user)
    {
        return Asset::find (
            [
                'status > 0 and user_id = ?0 and is_visible = 1', 'bind' => [$user->id]
            ]
        );
    }
}