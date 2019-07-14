<?php
namespace MtHash\Model\Asset;
use MtHash\Model\Mining\HASHContract;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class AssetRepository
{
    /**
     * @param Asset $asset
     * @return ResultsetInterface|HASHContract[]
     */
    static public function getInvestorsContracts (Asset $asset) : ResultsetInterface
    {
        return HASHContract::find (
            [
                'status > 0 and asset_id = ?0',
                'bind' => [$asset->id]
            ]
        );
    }


    static public function getInvestorsInvestment (Asset $asset)
    {
        $request = '
            SELECT `user_id`, SUM(`amount`) as `hash_invested`
            FROM `contract` 
            WHERE `status` > 0 and `asset_id` = ' . $asset->id . ' 
            GROUP by `user_id`
            HAVING SUM(`amount`) > 0
        ';

        $result     = \Phalcon\Di::getDefault()->get('db')->query ($request)->fetchAll (\PDO::FETCH_OBJ);
        return $result;

    }

    /**
     * @param string $symbol
     * @return \Phalcon\Mvc\Model|Asset
     * @throws \BusinessLogicException
     */
    static public function bySymbol (string $symbol)
    {
        return Asset::failFindFirst(
            [
                'status > 0 and symbol = ?0', 'bind' => [$symbol]
            ]
        );
    }




    static public function allSymbols() : array
    {
        return Asset::find (
            [
                'status > 0',
                'columns'   => 'symbol',
            ]
        )->toArray();
    }
}