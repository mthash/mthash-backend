<?php
namespace MtHash\Model\Historical;
use MtHash\Model\Asset\Units;
use MtHash\Model\Dashboard\Overview;
use MtHash\Model\Filter;
use MtHash\Model\Mining\Pool\Pool;

class DailyRevenue extends AbstractHistorical
{
    public $id, $user_id, $asset_id, $revenue;
    use \Timestampable;

    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_daily_revenue');
        $this->belongsTo ('asset_id', \MtHash\Model\Asset\Asset::class, 'id', ['alias' => 'asset']);
    }

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $chartData = $values = [];

        $seconds        = Units::periodToSeconds ($period);

        $now            = $originalNow = new \DateTime();
        $parentOriginPoint = new \DateTime('-' . $seconds . ' seconds');

        $interval       = $seconds > 3600 * 24 ? 'PT1H' : 'PT15M';
        $min            = 9999999999999999999999;
        $max            = 0;

        $request        = new Filter('', ['asset_id' => $assetId], ['asset_id']);

        foreach (Overview::find ($request) as $asset)
        {

            $assetData  =
                [
                    'id'                => $asset->asset->symbol,
                    'data'              => [],
                ];

            $chartData      = [];
            $originPoint    = clone $parentOriginPoint;

            while ($now > $originPoint)
            {
                $chartData[] =
                    [
                        'x'             => $originPoint->format(Units::DATETIME),
                        'y'             => $asset->daily_revenue,
                    ];

                $values[] = $asset->daily_revenue;

                if ($asset->daily_revenue < $min) $min = $asset->daily_revenue;
                if ($asset->daily_revenue > $max) $max = $asset->daily_revenue;

                $originPoint->add (new \DateInterval($interval));
            }

            $originPoint        = $parentOriginPoint;

            $assetData['data']  = $chartData;
            $data[] = $assetData;
        }



        return ['chart' => $data, 'min' => $min / 2, 'max' => $max * 2];
    }



}