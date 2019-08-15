<?php
namespace MtHash\Model\Mining;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\Units;
use MtHash\Model\Historical\Arcade;
use MtHash\Model\User\AssetRepository;
use MtHash\Model\User\User;
use MtHash\Model\User\WalletRepository;

class PortalDTO
{
    private $user, $assets, $byCurrency = [];

    public function __construct(User $user)
    {
        $this->user         = $user;
        $assetsRelations    = AssetRepository::allVisible($user);

        foreach ($assetsRelations as $relation)
        {
            $asset      = Asset::failFindFirst ($relation->asset_id);
            $historical = Arcade::changeToDay($this->user, $asset);

            $hashrate                   = $asset->getCurrentHashrate();
            $prettyHashrate             = Units::pretty($hashrate, 8);

            $singleUnit                 =
                [
                    'id'            => $asset->id,
                    'currency'      => $asset->symbol,
                    'algorithm'     => $asset->algo->name ?? 'N/A',
                    'value'         => $prettyHashrate['value'],
                    'unit'          => $prettyHashrate['unit'],
                    'shift'         => !empty ($historical->hashrate) ? Units::differencePercent($historical->hashrate, $hashrate) : 0,
                    'chart_data'    => $this->getChartData($asset),
                ];

            $this->assets[] = $singleUnit;
            $this->byCurrency[$asset->symbol] = $singleUnit;
        }
    }

    public function setData (array $investments)
    {
        foreach ($investments as $investment)
        {
            if ($investment->asset_id == 1) continue;

            $asset      = Asset::failFindFirst($investment->asset_id);

            $historical = Arcade::changeToDay($this->user, $asset);

            $hashrate                   = $asset->getCurrentHashrate();
            $prettyHashrate             = Units::pretty($hashrate, 8);

            $singleUnit                 =
                [
                    'id'            => $asset->id,
                    'currency'      => $asset->symbol,
                    'algorithm'     => $asset->algo->name ?? 'N/A',
                    'value'         => $prettyHashrate['value'],
                    'unit'          => $prettyHashrate['unit'],
                    'shift'         => !empty ($historical->hashrate) ? Units::differencePercent($historical->hashrate, $hashrate) : 0,
                    'chart_data'    => $this->getChartData($asset),
                ];

            $this->assets[] = $singleUnit;
            $this->byCurrency[$asset->symbol] = $singleUnit;
        }

    }

    private function getChartData(Asset $asset) : array
    {
        $data   = \MtHash\Model\Historical\Asset::generateChart('1d', $asset->id);

        $lastValue  = $data[count($data)-1]['y'] ?? 0;
        $preLastValue  = $data[count($data)-2]['y'] ?? 0;

        switch ($lastValue <=> $preLastValue)
        {
            case 0: $trend  = 'neutral'; break;
            case 1: $trend  = 'positive'; break;
            case -1: $trend = 'negative'; break;
            default:
                $trend  = 'neutral';
        }

        return
        [
            'id'            => $asset->id,
            'trend'         => $trend,
            'data'          => $data,
        ];
    }

    public function getAsset(string $currency)
    {
        return $this->byCurrency[$currency] ?? [];
    }

    public function getAssets()
    {
        return $this->assets;
    }




}