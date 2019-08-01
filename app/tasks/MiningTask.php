<?php
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Pool\Pool;
use MtHash\Model\Asset\AssetRepository;
use Phalcon\Cli\Task;
use MtHash\Model\Mining\Distributor;
use MtHash\Model\Mining\Relayer;

class MiningTask extends Task
{
    const MINERS_COUNT  = 1;

    public function startAction()
    {
        /**
         * @var $assets Asset[]
         */
        $assets = Asset::find (
            [
                'status > 0 and mineable = 1 and can_mine = 1',
            ]
        );

        if ($assets->count() > 0)
        {
            foreach ($assets as $asset)
            {
                /**
                 * @var $lastBlock \MtHash\Model\Mining\Block
                 */
                $lastBlock  = \MtHash\Model\Mining\Block::findFirst (
                    [
                        'asset_id = ?0 and status > 0', 'bind' => [$asset->id],
                        'order' => 'id DESC',
                    ]
                );

                echo $asset->symbol . "\n";

                if ($lastBlock && $lastBlock->count() > 0)
                {
                    $difference                 = time() - $lastBlock->created_at;

                    if (true == getenv('IS_PRODUCTION') && $difference < $asset->block_generation_time)
                    {
                        continue;
                    }
                }

                /**
                 * @var Pool $pool
                 */
                $pool   = Pool::findFirst (1);
                $pool->mine ($asset);

                Relayer::recalculateForAsset($asset);

                $distributor    = new Distributor();
                $distributor->distributeRewards($asset);

            }
        }
    }


    public function mineAction()
    {
        $asset  = func_get_arg(0)[0] ?? Asset::DEFAULT_ASSET;
        $asset  = AssetRepository::bySymbol($asset);

        /**
         * @var Pool $pool
         */
        $pool   = Pool::findFirst (1);
        $block  = $pool->mine ($asset);

        echo 'Block #' . $block->asset->symbol . '-' . $block->id . ' was generated <' . $block->hash . '>. Reward: ' . $block->reward . "\n";

        // @todo Move this to separate method
        Relayer::recalculateForAsset($asset);





        $distributor    = new Distributor();
        $distributor->distributeRewards($asset);

    }





}