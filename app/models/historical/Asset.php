<?php
namespace MtHash\Model\Historical;

class Asset extends AbstractHistorical
{
    use \Timestampable;
    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_asset');
    }
}