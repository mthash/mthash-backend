<?php
namespace MtHash\Model\Asset;
use MtHash\Model\User\Wallet;

interface Token
{
    public function transfer (Wallet $from, Wallet $to, int $amount) : string;

}