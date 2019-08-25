<?php
namespace MtHash\Model\Historical;

use MtHash\Model\Asset\Units;
use MtHash\Model\Filter;

/**
 * Class Arcade
 * @package MtHash\Model\Historical
 * @property \MtHash\Model\Asset\Asset $asset
 */
class Arcade extends AbstractHistorical
{
    use \Timestampable;

    public $id, $user_id, $asset_id, $revenue, $hashrate, $hash_invested, $balance;

    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_arcade');
        $this->belongsTo ('asset_id', \MtHash\Model\Asset\Asset::class, 'id', ['alias' => 'asset']);
    }

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $return = $values = [];

        $seconds        = Units::periodToSeconds ($period);
        $originPoint    = new \DateTime('-' . $seconds . ' seconds');

        $filtered       = new Filter(
            'status > 0',
            [
                'user_id'       => \Phalcon\Di::getDefault()->get('currentUser')->id,
                'created_at' => ['>=', $originPoint->getTimestamp()],
                'asset_id' => $assetId
            ],
            [
                'created_at', 'asset_id', 'user_id',
            ]
        );

        $arcade         = self::find (
            [
                $filtered->getRequest(), 'bind' => $filtered->getBind(),
            ]
        );

        foreach ($arcade as $item)
        {
            $data[$item->asset->symbol][] =
                [
                    'x'         => (new \DateTime('@' . $item->created_at))->format(Units::DATETIME),
                    'y'         => $item->hash_invested,
                ];

            $values[] = $item->hash_invested;
        }

        foreach ($data as $symbol => $chartData)
        {
            $return[] = ['id' => $symbol, 'data' => $chartData];
        }

        if (count ($values) < 1) $values[] = 0;

        return ['chart' => $return, 'min' => count ($values) > 0 ? min ($values) : 0, 'max' => count ($values) > 0 ? max ($values) : 0];
    }
}