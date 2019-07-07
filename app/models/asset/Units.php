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

}