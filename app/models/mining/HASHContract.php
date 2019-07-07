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

        $inputHashrate  = $asset->used_hashrate / $hashToken;

        if ($asset->used_hashrate + $inputHashrate > $asset->total_hashrate) throw new \BusinessLogicException('This Asset does not has enough hashrate');


        return true;
    }

    public function canWithdraw(Wallet $wallet, Asset $asset, float $hashToken): bool
    {
        if ($wallet->user_id != $this->getUser()->id) throw new \BusinessLogicException('Authorized user is not owner of the wallet');
        if ($wallet->currency != 'HASH') throw new \BusinessLogicException('Wallet must in HASH');

        $limit  = $this->getUserFrozenTokensAmountByAsset($asset);

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
                    'hashrate'              => $this->calculateHashrate($asset, $hashToken),
                ]
            );

            $this->increaseHashrate($asset, $this->hashrate);
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
                    'hashrate'              => -1 * $this->calculateHashrate($asset, $hashToken)
                ]
            );

            $this->decreaseHashrate($asset, $this->hashrate);

            return $this;
        }

        throw new \BusinessLogicException('Can not deposit tokens to wallet');
    }

    public function increaseHashrate(Asset $asset, float $hashrate): bool
    {
        $asset->used_hashrate+= $hashrate;
        return $asset->save();
    }

    public function decreaseHashrate(Asset $asset, float $hashrate): bool
    {
        $asset->used_hashrate+= $hashrate;
        return $asset->save();
    }

    public function getUsedHashrate (Asset $asset)
    {
        return self::sum (
            [
                'status > 0 and asset_id = ?0', 'bind' => [$asset->id],
                'column'        => 'hashrate',
            ]
        );
    }

    public function calculateHashrate (Asset $asset, int $hashTokens) : float
    {
        $unallocated    = $asset->total_hashrate - $asset->used_hashrate;
        return $unallocated / $hashTokens;
    }

    public function getUserFrozenTokensAmountByAsset (Asset $asset) : int
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