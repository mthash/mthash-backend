<?php
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\Pool\Pool;
use MtHash\Model\Asset\AssetRepository;
use Phalcon\Cli\Task;
use MtHash\Model\Mining\Distributor;

class MiningTask extends Task
{
    const MINERS_COUNT  = 1;

    public function mineAction()
    {
        $asset  = func_get_arg(0)[0] ?? Asset::DEFAULT_ASSET;
        $asset  = AssetRepository::bySymbol($asset);

        $relayer    = new \MtHash\Model\Mining\Relayer();
        $relayer->recalculateForAsset($asset);


        /**
         * @var Pool $pool
         */
        $pool   = Pool::findFirst (1);
        $block  = $pool->mine ($asset);

        echo 'Block #' . $block->asset->symbol . '-' . $block->id . ' was generated <' . $block->hash . '>. Reward: ' . $block->reward . "\n";

        // @todo Move this to separate method
        $relayer    = new \MtHash\Model\Mining\Relayer();
        $relayer->recalculateForAsset($asset);





        $distributor    = new Distributor();
        $distributor->distributeRewards($asset);

    }





}