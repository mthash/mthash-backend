<?php
namespace MtHash\Controller;
use MtHash\Model\Asset\Units;
use MtHash\Model\Historical\Arcade;
use MtHash\Model\Historical\DailyRevenue;
use MtHash\Model\Mining\Pool\Pool;

class TestController
{
    public function test()
    {
        $a = DailyRevenue::generateChart();
        echo '<pre>';
        print_r ($a);
    }
}