<?php
namespace MtHash\Model\User;
use MtHash\Model\AbstractEntity;

class Wallet extends AbstractEntity
{
    private $tokensOnRegistration   = ['HASH', 'ETH'];

    public $id, $user_id, $currency, $balance, $name;

    public function createFor (User $user) : bool
    {
        foreach ($this->tokensOnRegistration as $currency)
        {
            if (WalletRepository::userHasCurrency($user, $currency)) continue;

            (new self)->createEntity(
                [
                    'currency'              => $currency,
                    'user_id'               => $user->id,
                    'name'                  => 'My ' .  $currency . ' Wallet',
                ]
            );
        }

        return true;
    }

}