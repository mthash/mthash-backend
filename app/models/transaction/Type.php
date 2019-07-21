<?php
namespace MtHash\Model\Transaction;
class Type
{
    public $id, $name;

    const   P2P         = 1;
    const   MINING      = 2;
    const   FEE         = 3;
    const   BONUS       = 4;
    const   EXCHANGE    = 5;

    public function initialize()
    {
        $this->setSource ('transaction_type');
    }


}