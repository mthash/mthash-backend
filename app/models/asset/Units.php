<?php
namespace MtHash\Model\Asset;
class Units
{
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

    static public function pretty (float $number) : string
    {
        $possibleUnits  = array_reverse (array_keys (self::$suffixes));

        foreach ($possibleUnits as $unit)
        {
            if ($number / self::$prefixes[$unit] >= 0.5) return round ($number / self::$prefixes[$unit], 2) . ' ' . self::$suffixes[$unit];
        }

        return $number;
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

    static public function differencePercent ($firstNumber, $secondNumber) : string
    {
        if ($firstNumber == $secondNumber) return '0%';

        $percent    = round ($secondNumber * 100 / $firstNumber, 2);
        $sign       = $percent > 0 ? '+' : '';

        return  $sign . $percent . '%';
    }

}