<?php
namespace MtHash\Model\User;
class AssetRepository
{
    static public function find (User $user, Asset $asset) : Asset
    {
        $record = Asset::failFindFirst (
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
            $record->save();
        }

        return $record;

    }
}