<?php
namespace MtHash\Model\Mining\Pool;

use MtHash\Model\AbstractModel;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\Units;
use MtHash\Model\Filter;
use MtHash\Model\Mining\Block;
use MtHash\Model\Mining\Pool\Miner\Miner;
use Phalcon\Forms\Element\Date;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Class Pool
 * @package MtHash\Model\Mining\Pool
 * @property Miner|ResultsetInterface $miners
 * @property Asset $asset
 */

class Pool extends AbstractModel
{
    public $id, $name, $asset_id, $miners_count, $total_hashrate;

    use \Timestampable;

    public function initialize()
    {
        $this->setSource ('mining_pool');
        $this->hasMany ('id', Miner::class, 'pool_id', ['alias' => 'miners']);
        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
    }

    public function mine (Asset $asset)
    {
        $block      = new Block();
        $block->generate (Miner::findFirst (1), $asset);

        $asset->last_block_id   = $block->id;
        $asset->total_hashrate  = $this->total_hashrate; // @todo Change this when we will have multiple pools
        $asset->save();

        return $block;
    }

    public function addMiner (array $minerData) : Pool
    {
        $minerData['pool_id']   = $this->id;
        $miner  = new Miner();
        $miner->save ($minerData);

        $this->miners_count++;
        $this->total_hashrate+= $miner->max_hashrate;
        $this->save();

        return $this;
    }

    public function removeMiner (Miner $miner) : Pool
    {
        $miner->delete();

        $this->miners_count--;
        $this->total_hashrate-= $miner->max_hashrate;

        $this->save();

        return $this;
    }

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $chartData = $return = $values = [];

        $seconds        = Units::periodToSeconds ($period);

        $now            = new \DateTime();
        $originPoint    = new \DateTime('-' . $seconds . ' seconds');
        $values         = [];

        $filtered       = new Filter(
            'status > 0',
            [
                'created_at'    => ['>=', $originPoint->getTimestamp()],
                'asset_id'      => $assetId
            ],
            [
                'created_at', 'asset_id', 'user_id',
            ]
        );

        $arcade         = \MtHash\Model\Historical\Asset::find (
            [
                $filtered->getRequest(), 'bind' => $filtered->getBind(),
            ]
        );

        foreach ($arcade as $item)
        {
            $data[$item->asset->symbol][] =
                [
                    'x'         => (new \DateTime('@' . $item->created_at))->format(Units::DATETIME),
                    'y'         => $item->hashrate,
                ];

            $values[] = $item->hashrate;
        }

        foreach ($data as $symbol => $chartData)
        {
            $return[] =
                [
                    'id'            => $symbol,
                    'data'          => $chartData,
                ];
        }

        return ['chart' => $return, 'min' => min ($values), 'max' => max ($values)];

    }

    static public function generatePowerConsumptionChart (?string $period = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $chartData = $values = [];

        $seconds        = Units::periodToSeconds ($period);

        $now            = new \DateTime();
        $originPoint    = new \DateTime('-' . $seconds . ' seconds');

        $interval       = $seconds > 3600 * 24 ? 'PT1H' : 'PT15M';

        foreach (Asset::find ('status > 0 and can_mine = 1') as $asset)
        {
            if (!empty ($assetId) && $assetId != $asset->id) continue;

            $assetData  =
                [
                    'id'                => $asset->symbol,
                    'data'              => [],
                ];

            while ($now > $originPoint)
            {
                $chartData[] =
                    [
                        'x'             => $originPoint->format(Units::DATETIME),
                        'y'             => '10kW',
                    ];

                $values[] = '10kW';

                $originPoint->add (new \DateInterval($interval));
            }

            $assetData['data']  = $chartData;
            $data[] = $assetData;
        }



        return ['chart' => $data, 'min' => 5, 'max' => 15];
    }



}