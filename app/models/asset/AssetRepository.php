<?php
namespace MtHash\Model\Asset;
use MtHash\Model\Mining\Contract;
use MtHash\Model\Mining\HASHContract;
use Phalcon\Mvc\Model\ResultsetInterface;

class AssetRepository
{
    /**
     * @param Asset $asset
     * @return ResultsetInterface|HASHContract[]
     */
    static public function getInvestorsContracts (Asset $asset) : ResultsetInterface
    {
        return Contract::find (
            [
                'status > 0 and asset_id = ?0 and block_id <= ?1',
                'bind' => [$asset->id, $asset->last_block_id]
            ]
        );
    }
}