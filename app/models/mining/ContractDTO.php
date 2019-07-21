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
        $assets = Asset::find();
        foreach ($assets as $asset)
        {
            if ($asset->symbol == 'HASH') continue;
            $this->assets[$asset->symbol]   =
            [
                'revenue'               => 'TBA',
                'hash_invested'         => 0,
                'current_hashrate'      => 0,
                'asset'                 => $asset->toArray(['id', 'name', 'symbol', 'logo_url']),
                'balance'               => WalletRepository::byUserWithAsset($user, $asset)->balance,
            ];
        }

        $this->user = $user;
    }

    public function setData (array $investments)
    {
        foreach ($investments as $investment)
        {
            $asset  = Asset::failFindFirst($investment->asset_id);

            $this->assets[$asset->symbol]    = [
                'revenue'               => 'TBA',
                'hash_invested'         => $investment->hash_invested,
                'current_hashrate'      => Relayer::getUserCurrentHashrate($this->user, $asset),
                'asset'                 => $asset->toArray(['id', 'name', 'symbol', 'logo_url']),
                'balance'               => WalletRepository::byUserWithAsset($this->user, $asset)->balance,
            ];
        }
    }

    public function getAssets()
    {
        return $this->assets;
    }


}