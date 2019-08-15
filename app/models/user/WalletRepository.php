<?php
namespace MtHash\Model\User;
use Phalcon\Mvc\Model\ResultsetInterface;
use MtHash\Model\Asset\Asset;

class WalletRepository
{
    /**
     * @param User $user
     * @return ResultsetInterface|Wallet[]
     */
    static public function byUser (User $user) : ResultsetInterface
    {
        $wallets    = Wallet::find (
            [
                'status > 0 and user_id = ?0', 'bind' => [$user->id]
            ]
        );

        return $wallets;

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

    static public function getServiceWallet (string $service) : Wallet
    {
        return Wallet::failFindFirst (
            [
                'status > 0 and currency = ?0 and user_id = -1', 'bind' => [$service]
            ]
        );
    }

    static public function getRegistrationCurrencies() : array
    {
        $currencies    = array_map (function ($v){ return $v['symbol']; }, Asset::find()->toArray());
        $currencies[]  = 'HASH';
        return $currencies;
    }

    /**
     * @param User $user
     * @param string $currency
     * @return \MtHash\Model\AbstractModel|Wallet|\Phalcon\Mvc\Model
     * @throws \BusinessLogicException
     * @throws \ReflectionException
     */
    static public function currencyByUser (User $user, string $currency) : Wallet
    {
        $wallet = Wallet::failFindFirst(
            [
                'status > 0 and user_id = ?0 and currency = ?1', 'bind' => [$user->id, $currency]
            ]
        );

        return $wallet;
    }
}