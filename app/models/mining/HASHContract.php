<?php
namespace MtHash\Model\Mining;
use MtHash\Model\AbstractModel;
use MtHash\Model\Asset\Asset;
use MtHash\Model\User\Wallet;

class HASHContract extends AbstractModel implements Contract
{
    public $id, $wallet_id, $asset_id, $tokens_count, $hashrate;

    use \Timestampable;

    public function initialize()
    {
        $this->setSource ('contract');
    }

    public function canDeposit(Wallet $wallet, Asset $asset, float $hashToken): bool
    {
        if ($wallet->user_id != $this->getUser()->id) throw new \BusinessLogicException('Authorized user is not owner of the wallet');
        if ($wallet->currency != 'HASH') throw new \BusinessLogicException('Wallet must in HASH');
        if ($wallet->balance < $hashToken) throw new \BusinessLogicException('Insufficient HASH on Wallet');

        return true;
    }

    public function canWithdraw(Wallet $wallet, Asset $asset, float $hashToken): bool
    {
        if ($wallet->user_id != $this->getUser()->id) throw new \BusinessLogicException('Authorized user is not owner of the wallet');
        if ($wallet->currency != 'HASH') throw new \BusinessLogicException('Wallet must in HASH');

        $limit  = $this->getUserAllocatedTokens($asset);

        if ($hashToken > $limit) throw new \BusinessLogicException('Insufficient HASH on contract. Available: ' . $limit);

        return true;
    }

    public function deposit(Wallet $wallet, Asset $asset, float $hashToken): Contract
    {
        if (!$this->canDeposit($wallet, $asset, $hashToken)) throw new \BusinessLogicException('Can not deposit ' . $hashToken . ' HASH');

        if ($wallet->withdraw($hashToken))
        {
            // Freezing this tokens on SC
            $this->save (
                [
                    'user_id'               => $this->getUser()->id,
                    'wallet_id'             => $wallet->id,
                    'asset_id'              => $asset->id,
                    'amount'                => $hashToken,
                ]
            );

            $asset->hash_invested+= $hashToken;
            $asset->save();

            $this->hashrate = $this->calculateUserHashrate($asset);
            $this->save();



            return $this;
        }

        throw new \BusinessLogicException('Can not withdraw tokens from wallet');
    }

    public function withdraw(Wallet $wallet, Asset $asset, float $hashToken): Contract
    {
        if (!$this->canWithdraw($wallet, $asset, $hashToken)) throw new \BusinessLogicException('Can not deposit ' . $hashToken . ' HASH');
        if ($wallet->deposit($hashToken))
        {

            // Freezing this tokens on SC
            $this->save (
                [
                    'user_id'               => $this->getUser()->id,
                    'wallet_id'             => $wallet->id,
                    'asset_id'              => $asset->id,
                    'amount'                => -1 * $hashToken,
                ]
            );

            $this->hashrate = $this->calculateUserHashrate($asset);
            $this->save();

            $asset->hash_invested-= $hashToken;
            $asset->save();

            return $this;
        }

        throw new \BusinessLogicException('Can not deposit tokens to wallet');
    }

    public function getAllocatedTokens (Asset $asset)
    {
        return self::sum (
            [
                'status > 0 and asset_id = ?0', 'bind' => [$asset->id], 'column' => 'amount'
            ]
        );
    }

    public function calculateUserHashrate (Asset $asset) : int
    {
        $userTokens = $this->getUserAllocatedTokens($asset);
        if ($userTokens < 1) return 0;

        return $userTokens * $asset->total_hashrate / $asset->hash_invested;
    }

    public function getUserAllocatedTokens (Asset $asset) : int
    {
        $tokens = self::sum (
            [
                'status > 0 and asset_id = ?0 and user_id = ?1 and revoked_at IS NULL',
                'bind' => [$asset->id, $this->getUser()->id],
                'column'        => 'amount',
            ]
        );

        return (int) $tokens;
    }

}