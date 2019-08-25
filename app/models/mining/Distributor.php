<?php
namespace MtHash\Model\Mining;
use DateTime;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Transaction\Transaction;
use MtHash\Model\Transaction\Type;
use MtHash\Model\User\User;
use MtHash\Model\User\WalletRepository;
use MtHash\Model\Asset\AssetRepository;

class Distributor
{
    public function calculateSharesForAsset (Asset $asset) : SharesDTO
    {
        $contracts  = AssetRepository::getInvestorsContracts($asset);
        $dto        = new SharesDTO();

        foreach ($contracts as $contract)
        {
            $userShares = $this->calculateUserShares($contract->user, $asset);
            $dto->addUser($contract->user_id, $userShares);
        }

        return $dto;
    }

    public function calculateUserShares(User $user, Asset $asset)
    {
        $investedHashrates  = Relayer::byUser ($user, $asset);
        $currentHashrate    = $shares = 0;

        for ($i = 0; $i < count ($investedHashrates); $i++)
        {
            $currentHashrate+= $investedHashrates[$i]->hashrate;

            $nextInvestmentDate             = $investedHashrates[$i+1]->created_at ?? time();
            $currentInvestmentDate          = new DateTime('@' . $investedHashrates[$i]->created_at);
            $nextInvestmentDate             = new DateTime('@' . $nextInvestmentDate);
            $seconds                        = (int) $nextInvestmentDate->diff ($currentInvestmentDate)->format('%s');

            if ($currentHashrate > 0) $shares+= $currentHashrate * $seconds;
        }

        return $shares;
    }

    public function distributeRewards (Asset $asset)
    {
        $assetShares    = $this->calculateSharesForAsset($asset);
        $percent        = [];
        $totalShares    = $assetShares->getTotalShares();

        foreach ($assetShares->getUsers() as $userId => $shares)
        {
            if ($shares == 0) continue;
            $percent[$userId]   = $shares * 100 / $totalShares;
            $rewardsInToken     = $percent[$userId] * $asset->block_reward_amount / 100;

            $wallet = WalletRepository::byUserWithAsset(
                User::failFindFirst ($userId), $asset
            );



            $transaction    = new Transaction();
            $transaction->freeDeposit($asset, $wallet, $rewardsInToken, Type::MINING, $asset->last_block_id, $percent[$userId]);

            echo 'User ' . $userId . ' was deposited ' . $rewardsInToken . ' ' . $asset->symbol . '. Shares ' . $shares . ' of ' . $totalShares . ' (' . $percent[$userId] . '%) ' . "\n";

        }
    }


}