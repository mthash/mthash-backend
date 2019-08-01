<?php
namespace MtHash\Model\Historical;

class Arcade extends AbstractHistorical
{
    use \Timestampable;

    public $id, $user_id, $asset_id, $revenue, $hashrate, $hash_invested, $balance;

    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_arcade');
    }
}