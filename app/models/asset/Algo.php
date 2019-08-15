<?php
namespace MtHash\Model\Asset;
use MtHash\Model\AbstractEntity;
use MtHash\Model\Filter;

class Algo extends AbstractEntity
{
    public $id, $name;
    public function initialize()
    {
        parent::initialize();
    }

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';

        $return         = $data = $chartData = $values = [];
        $seconds        = Units::periodToSeconds ($period);

        $originPoint    = new \DateTime('-' . $seconds . ' seconds');


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
}