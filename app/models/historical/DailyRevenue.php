<?php
namespace MtHash\Model\Historical;
use MtHash\Model\Asset\Units;
use MtHash\Model\Filter;

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
        $data   = $return = $values = [];

        $seconds        = Units::periodToSeconds ($period);
        $originPoint    = new \DateTime('-' . $seconds . ' seconds');

        $filtered       = new Filter(
            'status > 0',
            [
                'user_id'       => \Phalcon\Di::getDefault()->get('currentUser')->id,
                'created_at'    => ['>=', $originPoint->getTimestamp()],
                'asset_id'      => $assetId
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
                    'y'         => number_format ($item->revenue, 0, '.', ','),
                ];

            $values[] = $item->revenue;
        }

        foreach ($data as $symbol => $chartData)
        {
            $return[] =
                [
                    'id'            => $symbol,
                    'data'          => $chartData,
                ];
        }

        if (count ($values) < 1) $values[] = 0;

        return ['chart' => $return, 'min' => min ($values), 'max' => max ($values)];
    }



}