<?php
namespace MtHash\Model\Mining;
use MtHash\Model\Asset\Asset;
use MtHash\Model\User\User;
use Phalcon\Mvc\Model\ResultsetInterface;

class ContractRepository
{
    /**
     * @param User $user
     * @param Asset $asset
     * @return ResultsetInterface|HASHContract[]
     */
    static public function currentHashrates (User $user, Asset $asset) : ResultsetInterface
    {
        return HASHContract::find (
            [
                'status > 0 and user_id = ?0 and asset_id = ?1',
                'bind' => [$user->id, $asset->id]
            ]
        );
    }
}