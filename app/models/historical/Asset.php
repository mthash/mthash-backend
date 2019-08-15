<?php
namespace MtHash\Model\Historical;

use MtHash\Model\Asset\Units;
use MtHash\Model\Filter;

class Asset extends AbstractHistorical
{
    use \Timestampable;
    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_asset');
        $this->belongsTo ('asset_id', \MtHash\Model\Asset\Asset::class, 'id', ['alias' => 'asset']);
    }

    static public function lastHashrateValue (int $assetId) : int
    {
        return (int) self::findFirst (
            [
                'status > 0 and asset_id = ?0',
                'bind' => [$assetId],
                'order' => 'id DESC'
            ]
        )->hashrate;
    }

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $return = $values = [];

        $seconds        = Units::periodToSeconds ($period);
        $originPoint    = new \DateTime('-' . $seconds . ' seconds');

        $data         = self::find (
            [
                'status > 0 and asset_id = ?0 and created_at >= ?1',
                'bind' => [$assetId, $originPoint->getTimestamp()]
            ]
        );

        foreach ($data as $point)
        {
            $return[] =
                [
                    'x'         => (new \DateTime('@' . $point->created_at))->format (Units::DATETIME),
                    'y'         => $point->hashrate,
                ];

            $values[] = $point->hashrate;
        }

        return ['chart' => $return, 'min' => min ($values), 'max' => max ($values)];
    }
}