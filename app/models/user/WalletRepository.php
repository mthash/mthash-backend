<?php
namespace MtHash\Model\User;
use Phalcon\Mvc\Model\ResultsetInterface;
use MtHash\Model\Asset\Asset;

class WalletRepository
{
    static public function byUser (User $user) : ResultsetInterface
    {
        return Wallet::find (
            [
                'status > 0 and user_id = ?0', 'bind' => [$user->id],
            ]
        );
    }

    static public function byUserWithAsset (User $user, Asset $asset) : Wallet
    {
        return Wallet::failFindFirst(
            [
                'status > 0 and user_id = ?0 and asset_id = ?1',
                'bind' => [$user->id, $asset->id]
            ]
        );
    }

    static public function userHasCurrency (User $user, string $currency) : bool
    {
        $wallets    = self::byUser ($user);

        if ($wallets && $wallets->count() > 0)
        {
            $currencies = array_map (function ($v){ return $v['currency']; }, $wallets->toArray('currency'));
            return in_array ($currency, $currencies);
        }

        return false;
    }

    static public function getServiceWallet (string $service)
    {
        return Wallet::failFindFirst (
            [
                'status > 0 and name = ?0 and user_id = 1', 'bind' => [$service]
            ]
        );
    }
}