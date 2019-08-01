<?php
use MtHash\Model\Mining\ContractRepository;
use MtHash\Model\Mining\Relayer;
use MtHash\Model\Historical\Arcade;
use MtHash\Model\Historical\Wallet;
use MtHash\Model\Historical\Asset;

class HistoryTask extends \Phalcon\Cli\Task
{
    public function restartAction()
    {
        \Phalcon\Di::getDefault()->get('db')->query ('TRUNCATE TABLE `history_arcade`');
        \Phalcon\Di::getDefault()->get('db')->query ('TRUNCATE TABLE `history_wallet`');
        \Phalcon\Di::getDefault()->get('db')->query ('TRUNCATE TABLE `history_asset`');

        $this->watchAction();
    }

    public function watchAction()
    {

        $this->watchArcade();
        $this->watchWallets();
        $this->watchAssets();

    }

    private function watchAssets()
    {
        $assets     = \MtHash\Model\Asset\Asset::find (
            [
                'status > 0',
            ]
        );

        foreach ($assets as $asset)
        {
            (new Asset())->createEntity(
                [
                    'asset_id'          => $asset->id,
                    'tokens_invested'   => $asset->hash_invested,
                    'hashrate'          => $asset->getUsingHashrate(),
                ]
            );
        }
    }

    private function watchArcade()
    {
        $contracts  = Relayer::find(
            [
                'status > 0',
                'group' => 'asset_id, user_id',
                'order' => 'id DESC',
            ]
        );

        foreach ($contracts as $contract)
        {
            (new Arcade())->createEntity(
                [
                    'revenue'           => round (\MtHash\Model\User\Asset::calculateRevenue($contract->user, $contract->asset), 4),
                    'user_id'           => $contract->user_id,
                    'asset_id'          => $contract->asset_id,
                    'hashrate'          => Relayer::getUserCurrentHashrate($contract->user, $contract->asset),
                    'balance'           => $contract->user->getWallet($contract->asset->symbol)->balance,
                    'hash_invested'     => ContractRepository::getUserInvestedHashByAsset($contract->user, $contract->asset),
                ]
            );
        }
    }

    private function watchWallets() : void
    {
        $wallets    = \MtHash\Model\User\Wallet::find (
            [
                'status > 0 and user_id > 0'
            ]
        );

        foreach ($wallets as $wallet)
        {
            (new Wallet())->createEntity(
                [
                    'wallet_id'         => $wallet->id,
                    'user_id'           => $wallet->user_id,
                    'balance'           => $wallet->balance,
                ]
            );
        }
    }
}