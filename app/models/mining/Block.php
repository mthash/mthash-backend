<?php
namespace MtHash\Model\Mining;
use MtHash\Model\AbstractModel;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Pool\Miner\Miner;
use MtHash\Model\User\User;

/**
 * Class Block
 * @package MtHash\Model\Mining
 * @property User $user
 * @property \Pool $pool
 */
class Block extends AbstractModel
{
    public $id, $asset_id, $miner_id, $pool_id, $hash, $reward;

    use \Timestampable;

    public function generate(Miner $miner, Asset $asset) : Block
    {
        $this->save (
            [
                'asset_id'          => $asset->id,
                'miner_id'          => $miner->id,
                'pool_id'           => $miner->pool_id,
                'hash'              => $this->getBlockHash(),
                'reward'            => $this->getBlockReward($asset),
            ]
        );

        return $this;
    }

    private function getBlockReward (Asset $asset)
    {
        return $asset->block_reward_amount;
    }

    private function getBlockHash()
    {
        return hash ('SHA256', microtime(true));
    }

    /**
     * 0. Майнит — пул. Пока только один.
     * 1. Сделать возможность пулу принимать хешрейт
     * 2. Сделать возможность пулу отпускать хешрейт
     * 3. Сделать возможность генерации блоков демоном
     * 4. Сделать BlockReward который раздаёт каждому кошельку токены
     * 5. Сделать статистику
     * 6. Сделать АПИ
     * 7. Сделать миграции
     */

}