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
                'group' => 'user_id',
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
     * @return \MtHash\Model\AbstractEntity|Asset|\Phalcon\Mvc\Model
     * @throws \BusinessLogicException
     * @throws \ReflectionException
     */
    static public function bySymbol (string $symbol)
    {
        return Asset::failFindFirst(
            [
                'status > 0 and symbol = ?0', 'bind' => [$symbol]
            ]
        );
    }

    /**
     * @return \MtHash\Model\AbstractModel[]|Asset[]|ResultsetInterface|void
     */
    static public function getMineable()
    {
        return Asset::find (
            [
                'status > 0 and mineable = 1 and can_mine = 1',
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