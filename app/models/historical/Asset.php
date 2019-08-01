<?php
namespace MtHash\Model\Historical;
use MtHash\Model\AbstractEntity;

class Asset extends AbstractEntity
{
    use \Timestampable;
    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_asset');
    }
}