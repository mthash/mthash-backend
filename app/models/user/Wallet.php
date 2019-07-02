<?php
namespace MtHash\Model\User;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\Eth\Address;

/**
 * Class Wallet
 * @package MtHash\Model\User
 * @property Asset $asset
 */
class Wallet extends AbstractEntity
{
    private $tokensOnRegistration   = ['HASH', 'ETH'];

    public $id, $user_id, $currency, $balance, $name;

    public function createFor (User $user) : bool
    {
        foreach ($this->tokensOnRegistration as $currency)
        {
            if (WalletRepository::userHasCurrency($user, $currency)) continue;
            $address    = Address::generate();

            (new self)->createEntity(
                [
                    'currency'              => $currency,
                    'user_id'               => $user->id,
                    'name'                  => 'My ' .  $currency . ' Wallet',
                    'address'               => $address['address'],
                    'public_key'            => $address['public'],
                    'private_key'           => $address['private'],
                ]
            );
        }

        return true;
    }

    public function canSendTo (Wallet $wallet, int $amount) : bool
    {
        if ($this->currency != $wallet->currency) throw new \BusinessLogicException('Can not send tokens to other token currency');
        if ($this->balance < $amount) throw new \BusinessLogicException('Insufficient funds');
    }

    public function deposit (int $amount) : Wallet
    {
        $this->balance+= $amount;
        $this->save();
        return $this;
    }

    public function withdraw (int $amount) : Wallet
    {
        $this->balance -= $amount;
        $this->save();
        return $this;
    }

}