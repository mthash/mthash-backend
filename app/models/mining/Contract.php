<?php
namespace MtHash\Model\Mining;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Transaction\Transaction;
use MtHash\Model\User\Wallet;

interface Contract
{
    public function canDeposit (Wallet $wallet, Asset $asset, float $hashToken) : bool;
    public function canWithdraw (Wallet $wallet, Asset $asset, float $hashToken) : bool;

    public function deposit (Wallet $wallet, Asset $asset, float $hashToken) : Contract;
    public function withdraw (Wallet $wallet, Asset $asset, float $hashToken) : Contract;

    public function getAllocatedTokens (Asset $asset);
    public function calculateUserHashrate (Asset $asset);
    public function getUserAllocatedTokens (Asset $asset);

}