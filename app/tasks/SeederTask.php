<?php
class SeederTask extends \Phalcon\Cli\Task
{
    private function truncate (\Phalcon\Mvc\Model $model) : void
    {
        $this->getDI()->get('db')->query ('TRUNCATE TABLE `' . $model->getSource() . '`');
    }

    public function restartAction()
    {
        if (false == getenv('IS_PRODUCTION'))
        {
            $this->truncate((new \MtHash\Model\User\Asset()));
            $this->truncate((new \MtHash\Model\Asset\Asset()));
            $this->truncate((new \MtHash\Model\User\Wallet()));
            $this->truncate((new \MtHash\Model\Mining\Block()));
            $this->truncate((new \MtHash\Model\Mining\HASHContract()));
            $this->truncate((new \MtHash\Model\Transaction\Transaction()));
            $this->truncate((new \MtHash\Model\Mining\Relayer()));
            $this->truncate((new \MtHash\Model\Mining\Pool\Pool()));

            (new HistoryTask())->restartAction();

            $this->poolsAction();
            $this->assetsAction();
            $this->usersAction();
            $this->walletsAction();

        }
        else
        {
            echo 'You can not restart on production';
        }
    }

    public function poolsAction()
    {
        $pools  =
            [
                ['name' => 'SHA-256 Pool', 'miners_count' => 1, 'total_hashrate' => 194440000000000000, 'used_power' => 9400000],
                ['name' => 'ETHash Pool', 'miners_count' => 1, 'total_hashrate' => 2425000000000, 'used_power' => 4250000],
                ['name' => 'Equihash Pool', 'miners_count' => 1, 'total_hashrate' => 360000000, 'used_power' => 5350000],
                ['name' => 'SCRYPT Pool', 'miners_count' => 1, 'total_hashrate' => 4428000000000, 'used_power' => 4400000],
            ];

        foreach ($pools as $pool)
        {
            (new \MtHash\Model\Mining\Pool\Pool())->create($pool);
        }
    }

    public function assetsAction()
    {
        $assets     =
            [
                'HASH'          => ['name' => 'MtHash', 'price_usd' => 1, 'mineable' => 0, 'can_mine' => 0],
                'ETH'           => ['name' => 'Ethereum', 'price_usd' => 221, 'block_generation_time' => 600, 'block_reward_amount' => 3, 'algo_id' => 3],
                'BTC'           => ['name' => 'Bitcoin', 'price_usd' => 10432, 'block_generation_time' => 600, 'block_reward_amount' => 12.5, 'algo_id' => 1],
                'LTC'           => ['name' => 'Litecoin', 'price_usd' => 98, 'block_generation_time' => 600, 'block_reward_amount' => 12.5, 'algo_id' => 2],
                'BCH'           => ['name' => 'Bitcoin Cash', 'price_usd' => 315, 'block_generation_time' => 600, 'block_reward_amount' => 12.5, 'algo_id' => 1],
                //'ADA'           => ['name' => 'Cardano', 'price_usd' => 0.06, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'TRX'           => ['name' => 'TRON', 'price_usd' => 0.02, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'XMR'           => ['name' => 'Monero', 'price_usd' => 83, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'DASH'          => ['name' => 'Dash', 'price_usd' => 116, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'ETC'           => ['nam  e' => 'Ethereum Classic', 'price_usd' => 6.2, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                'ZEC'           => ['name' => 'ZCash', 'price_usd' => 49.49, 'block_generation_time' => 600, 'block_reward_amount' => 10, 'algo_id' => 4],
            ];

        $this->truncate((new \MtHash\Model\Asset\Asset()));

        foreach ($assets as $symbol => $data)
        {
            $asset          = new \MtHash\Model\Asset\Asset();
            $data['symbol'] = $symbol;
            $data['mineable'] = $data['can_mine'] = $symbol == 'HASH' ? 0 : 1;

            switch ($symbol)
            {
                case 'BTC':
                case 'BCH':
                    $poolId = 1;
                break;

                case 'ETH': $poolId = 2; break;
                case 'LTC': $poolId = 4; break;
                case 'ZEC': $poolId = 3; break;
                default:
                    $poolId = 1;
            }



            $data['total_hashrate'] = \MtHash\Model\Mining\Pool\Pool::findFirst($poolId)->total_hashrate;
            $data['last_block_id'] = 0;
            $asset->createEntity($data);
        }
    }

    public function walletsAction()
    {
        $this->truncate((new \MtHash\Model\User\Wallet()));

        foreach (\MtHash\Model\Asset\Asset::find() as $asset)
        {
            $address    = \MtHash\Model\Asset\Eth\Address::generate();
            $wallet     = new \MtHash\Model\User\Wallet();
            $wallet->asset_id   = $asset->id;
            $wallet->user_id    = -1;
            $wallet->address    = $address['address'];
            $wallet->public_key = $address['public'];
            $wallet->private_key= $address['private'];
            $wallet->currency   = $asset->symbol;
            $wallet->name       = $asset->symbol . ' Service Wallet';
            $wallet->balance    = 99999999999;
            $wallet->save();
        }

        // Creating wallets for all registered users
        foreach (\MtHash\Model\User\User::find('status > 0') as $user)
        {
            $wallet = new \MtHash\Model\User\Wallet();
            $wallet->createFor ($user);
        }
    }

    public function quotesAction()
    {
        /**
         * @var \MtHash\Model\Asset\Asset[] $assets
         */
        $assets     = \MtHash\Model\Asset\Asset::find ('mineable = 1');
        $quotes     = (new \MtHash\Model\Asset\CMC($assets))->getResponse();

        print_r ($quotes);

        foreach ($assets as $asset)
        {
            $asset->price_usd   = $quotes->data->{$asset->symbol}->quote->USD->price;
            $asset->save();

            echo $asset->symbol . ' new price is set to ' . $asset->price_usd . "\n";
        }
    }

    public function usersAction()
    {
        try
        {
            $user   = new \MtHash\Model\User\User();
            $user->createDemo();
        }
        catch (\Throwable $e)
        {
            echo $e->getMessage();
        }
    }

    // This is one-time call method to migrate from existing scheme of assets ownership to the new one.
    // It shouldn't be called and could be removed safely after successful demo
    public function migrate_assetsAction()
    {
        $assets = \MtHash\Model\Mining\HASHContract::find (
            [
                'status > 0',
                'group' => ['user_id', 'asset_id']
            ]
        );

        $this->truncate(new \MtHash\Model\User\Asset());

        foreach ($assets as $relation)
        {
            (new \MtHash\Model\User\Asset())->createEntity(
                [
                    'asset_id'          => $relation->asset_id,
                    'user_id'           => $relation->user_id,
                    'is_visible'        => 1,
                ]
            );
        }
    }
}