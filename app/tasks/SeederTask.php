<?php
class SeederTask extends \Phalcon\Cli\Task
{
    private function truncate (\Phalcon\Mvc\Model $model) : void
    {
        $this->getDI()->get('db')->query ('TRUNCATE TABLE `' . $model->getSource() . '`');
    }

    public function assetsAction()
    {
        $assets     =
            [
                'HASH'          => ['name' => 'MtHash', 'price_usd' => 1, 'mineable' => 0, 'can_mine' => 0],
                'ETH'           => ['name' => 'Ethereum', 'price_usd' => 221, 'block_generation_time' => 600, 'block_reward_amount' => 3],
                'BTC'           => ['name' => 'Bitcoin', 'price_usd' => 10432, 'block_generation_time' => 600, 'block_reward_amount' => 12.5],
                'LTC'           => ['name' => 'Litecoin', 'price_usd' => 98, 'block_generation_time' => 600, 'block_reward_amount' => 12.5],
                'BCH'           => ['name' => 'Bitcoin Cash', 'price_usd' => 315, 'block_generation_time' => 600, 'block_reward_amount' => 12.5],
                'ADA'           => ['name' => 'Cardano', 'price_usd' => 0.06, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                'TRX'           => ['name' => 'TRON', 'price_usd' => 0.02, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                'XMR'           => ['name' => 'Monero', 'price_usd' => 83, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                'DASH'          => ['name' => 'Dash', 'price_usd' => 116, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                'ETC'           => ['name' => 'Ethereum Classic', 'price_usd' => 6.2, 'block_generation_time' => 600, 'block_reward_amount' => 10],
            ];

        $this->truncate((new \MtHash\Model\Asset\Asset()));

        foreach ($assets as $symbol => $data)
        {
            $asset          = new \MtHash\Model\Asset\Asset();
            $data['symbol'] = $symbol;
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
    }
}