<?php
namespace MtHash\Model\Mining;
use MtHash\Model\Asset\Asset;
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

            $asset  = Asset::failFindFirst($investment->asset_id);

            $this->assets[$asset->symbol]    = [
                'revenue'               => round (\MtHash\Model\User\Asset::calculateRevenue($this->user, $asset), 4),
                'hash_invested'         => $investment->hash_invested,
                'current_hashrate'      => Relayer::getUserCurrentHashrate($this->user, $asset),
                'asset'                 => $asset->toArray(['id', 'symbol']),
                'balance'               => WalletRepository::byUserWithAsset($this->user, $asset)->balance,
            ];
        }
    }

    public function getAssets()
    {
        return $this->assets;
    }


}