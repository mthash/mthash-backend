<?php
namespace MtHash\Model\Historical;
use MtHash\Model\AbstractEntity;

class Arcade extends AbstractEntity
{
    use \Timestampable;
    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_arcade');
    }
}