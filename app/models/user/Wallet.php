<?php
namespace MtHash\Model\User;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\Eth\Address;

/**
 * Class Wallet
 * @package MtHash\Model\User
 * @property Asset $asset
 * @property User $user
 */
class Wallet extends AbstractEntity
{
    public $id, $asset_id, $address, $public_key, $private_key, $user_id, $currency, $balance, $name;

    public function initialize()
    {
        parent::initialize();
        $this->belongsTo ('user_id', User::class, 'id', ['alias' => 'user']);
        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
    }

    public function createFor (User $user) : bool
    {
        foreach (Asset::find() as $asset)
        {
            if (WalletRepository::userHasCurrency($user, $asset->symbol)) continue;
            $address    = Address::generate();

            (new self)->createEntity(
                [
                    'asset_id'              => $asset->id,
                    'currency'              => $asset->symbol,
                    'user_id'               => $user->id,
                    'name'                  => 'My ' .  $asset->symbol . ' Wallet',
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
        if ($amount < 0) throw new \BusinessLogicException('You can not send negative amount of tokens');
        return true;
    }

    public function deposit (float $amount) : Wallet
    {
        $this->balance+= $amount;
        $this->save();
        return $this;
    }

    public function withdraw (float $amount) : Wallet
    {
        $this->balance -= $amount;
        $this->save();
        return $this;
    }

}