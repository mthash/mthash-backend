<?php
namespace MtHash\Controller;
use MtHash\Model\Asset\Units;

class TestController
{
    public function test()
    {
        echo Units::pretty($_GET['number']);
    }
}