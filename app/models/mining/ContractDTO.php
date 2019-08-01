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

            $historical = Arcade::changeToDay($this->user);

            $hashrate                   = Relayer::getUserCurrentHashrate($this->user, $asset);

            $this->assets[$asset->symbol]    = [
                'revenue'               => round (\MtHash\Model\User\Asset::calculateRevenue($this->user, $asset), 4),
                'hash_invested'         => $investment->hash_invested,
                'current_hashrate'      => Units::pretty($hashrate),
                'asset'                 => [
                    'id'                    => $asset->id,
                    'symbol'                => $asset->symbol,
                    'algo'                  => $asset->algo->name,
                ],
                'balance'               => WalletRepository::byUserWithAsset($this->user, $asset)->balance,
            ];

            // Quick Access
            $q  = $this->assets[$asset->symbol];

            $this->assets[$asset->symbol]['change_day'] =
                [
                    'revenue'               => !empty ($historical->revenue) ? Units::differencePercent($historical->revenue, $q['revenue']) : 0,
                    'hash_invested'         => !empty ($historical->hash_invested) ? Units::differencePercent($historical->hash_invested, $q['hash_invested']) : 0,
                    'current_hashrate'      => !empty ($historical->hashrate) ? Units::differencePercent($historical->hashrate, $hashrate) : 0,
                    'balance'               => !empty ($historical->balance) ? Units::differencePercent($historical->balance, $q['balance']) : 0,
                ];
        }
    }

    public function getAssets()
    {
        return $this->assets;
    }


}