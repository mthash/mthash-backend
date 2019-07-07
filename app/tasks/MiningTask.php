<?php
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\Units;
use MtHash\Model\Mining\Pool\Pool;

class MiningTask extends \Phalcon\Cli\Task
{
    const MINERS_COUNT  = 100;

    public function mine (Asset $asset)
    {
        /**
         * @var Pool $pool
         */
        $pool   = Pool::findFirst (1);
        $pool->mine ($asset);









    }



}