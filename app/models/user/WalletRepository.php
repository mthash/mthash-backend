<?php
namespace MtHash\Model\User;
use Phalcon\Mvc\Model\ResultsetInterface;

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
}