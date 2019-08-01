<?php
namespace MtHash\Model\Historical;

class Wallet extends AbstractHistorical
{
    use \Timestampable;
    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_wallet');
    }
}