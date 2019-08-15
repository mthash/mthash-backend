<?php
namespace MtHash\Model\Mining;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\Units;
use MtHash\Model\Historical\Arcade;
use MtHash\Model\User\User;
use MtHash\Model\User\WalletRepository;

class ContractDTO
{
    private $assets, $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function setData (array $investments)
    {
        foreach ($investments as $investment)
        {
            if ($investment->asset_id == 1) continue;

            $asset      = Asset::failFindFirst($investment->asset_id);
            $historical = Arcade::changeToDay($this->user, $asset);


            $hashrate                   = Relayer::getUserCurrentHashrate($this->user, $asset);
            $revenue                    = round (\MtHash\Model\User\Asset::calculateRevenue($this->user, $asset), 4);
            $prettyHashrate             = Units::pretty($hashrate);




            $singleUnit    = [
                'id'                    => $asset->id,
                'currency'              => $asset->symbol,

                'revenue'               =>
                [
                    'value'     => $revenue,
                    'unit'      => '/hr',
                    'shift'     => 0,
                ],

                'hashrate'              =>
                [
                    'value'     => $prettyHashrate['value'],
                    'unit'      => $prettyHashrate['unit'],
                    'shift'     => 0,
                ],

                'mining'                =>
                [
                    'value'     => number_format ($investment->hash_invested, 0, '.', ','),
                    'unit'      => 'HASH',
                    'shift'     => 0,
                ],

                'balance'               =>
                [
                    'value'     => WalletRepository::byUserWithAsset($this->user, $asset)->balance,
                    'unit'      => $asset->symbol,
                    'shift'     => 0,
                ],
            ];

            $singleUnit['revenue']['shift']     = !empty ($historical->revenue) ? Units::differencePercent($historical->revenue, $singleUnit['revenue']['value']) : 0;
            $singleUnit['hashrate']['shift']    = !empty ($historical->hashrate) ? Units::differencePercent($historical->hashrate, $hashrate) : 0;
            $singleUnit['mining']['shift']      = !empty ($historical->hash_invested) ? Units::differencePercent($historical->hash_invested, $singleUnit['mining']['value']) : 0;
            $singleUnit['balance']['shift']     = !empty ($historical->balance) ? Units::differencePercent($historical->balance, $singleUnit['balance']['value']) : 0;

            $this->assets[] = $singleUnit;
        }
    }

    public function getAssets()
    {
        return $this->assets;
    }


}