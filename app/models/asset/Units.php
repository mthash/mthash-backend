<?php
namespace MtHash\Model\Asset;
class Units
{
    const   DATETIME    = 'Y-m-d h:i:s A';

    static private $prefixes    =
        [
            'K'         => 1000,
            'M'         => 1000 * 1000,
            'G'         => 1000 * 1000 * 1000,
            'T'         => 1000 * 1000 * 1000 * 1000,
        ];

    static private $suffixes    =
        [
            'K'             => 'KH/s',
            'M'             => 'MH/s',
            'G'             => 'GH/s',
            'T'             => 'TH/s',
        ];

    static public function pretty (float $number, int $roundPoints = 2) : array
    {
        $possibleUnits  = array_reverse (array_keys (self::$suffixes));

        foreach ($possibleUnits as $unit)
        {
            if ($number / self::$prefixes[$unit] >= 0.5)
            {
                $result = round ($number / self::$prefixes[$unit], $roundPoints);
                return
                [
                    'value'     => $result,
                    'raw'       => $number,
                    'formatted' => $result . ' ' . self::$suffixes[$unit],
                    'unit'      => self::$suffixes[$unit],
                ];
            }
        }

        return ['raw' => $number, 'value' => $number, 'formatted' => $number . ' H/s', 'unit' => 'H/s'];
    }

    static public function toHashPerSecond (float $number, string $fromUnit) : float
    {
        if (!in_array ($fromUnit, array_keys (self::$prefixes))) throw new \BusinessLogicException('Unknown unit ' . $fromUnit);
        return $number * self::$prefixes[$fromUnit];
    }

    static public function HPS (float $number, string $fromUnit, string $toUnit) : float
    {
        if (!in_array ($toUnit, array_keys (self::$prefixes))) throw new \BusinessLogicException('Unknown unit ' . $toUnit);

        $HPS    = self::toHashPerSecond($number, $fromUnit);

        return $HPS / self::$prefixes[$toUnit];
    }

    static public function differencePercent ($firstNumber, $secondNumber) : float
    {
        if ($firstNumber == $secondNumber) return 0;

        $percent    = round ((float) $firstNumber * 100 / (float) $secondNumber, 2);
        return $percent;
    }

    static public function periodToSeconds (string $period) : int
    {
        $period = strtolower ($period);

        switch ($period)
        {
            case '1h': return 3600; break;
            case '3h': return 3600 * 3; break;
            case '1d': return 3600 * 24; break;
            case '7d': return 3600 * 24 * 7; break;
            case '1m': return 3600 * 24 * 30; break;
            default:
                return -1;
        }
    }

}