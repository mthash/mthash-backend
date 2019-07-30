<?php
namespace MtHash\Model\Mining;
use MtHash\Model\AbstractModel;
use MtHash\Model\Asset\Asset;
use MtHash\Model\User\User;
use MtHash\Model\User\Wallet;

/**
 * Class HASHContract
 * @package MtHash\Model\Mining
 * @property User $user
 * @property Asset $asset
 * @property Wallet $wallet
 * @property Block $block
 */
class HASHContract extends AbstractModel implements Contract
{
    public $id, $wallet_id, $user_id, $asset_id, $tokens_count, $hashrate, $block_id;

    use \Timestampable;

    public function initialize()
    {
        $this->setSource ('contract');
        $this->belongsTo ('user_id', User::class, 'id', ['alias' => 'user']);
        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
        $this->belongsTo ('wallet_id', Wallet::class, 'id', ['alias' => 'wallet']);
        $this->belongsTo ('block_id', Block::class, 'id', ['alias' => 'block']);
    }

    public function canDeposit(Wallet $wallet, Asset $asset, float $hashToken): bool
    {
        if ($wallet->user_id != $this->getUser()->id) throw new \BusinessLogicException('Authorized user is not owner of the wallet');
        if ($wallet->currency != 'HASH') throw new \BusinessLogicException('Wallet must in HASH');
        if ($wallet->balance < $hashToken) throw new \BusinessLogicException('Insufficient HASH on Wallet');
        if ($hashToken <= 0) throw new \BusinessLogicException('Amount can not be less or equals zero');

        return true;
    }

    public function canWithdraw(Wallet $wallet, Asset $asset, float $hashToken): bool
    {
        if ($wallet->user_id != $this->getUser()->id) throw new \BusinessLogicException('Authorized user is not owner of the wallet');
        if ($wallet->currency != 'HASH') throw new \BusinessLogicException('Wallet must in HASH');
        if ($hashToken <= 0) throw new \BusinessLogicException('Amount can not be less or equals zero');

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
                    'block_id'              => $asset->last_block_id,
                ]
            );

            $asset->hash_invested+= $hashToken;
            $asset->save();

            $this->hashrate = $this->calculateUserHashrate($asset);
            $this->save();

            Relayer::recalculateForAsset($asset);

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
                    'block_id'              => $asset->last_block_id,
                ]
            );

            $this->hashrate = $this->calculateUserHashrate($asset);
            $this->save();

            $asset->hash_invested-= $hashToken;
            $asset->save();

            Relayer::recalculateForAsset($asset);

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