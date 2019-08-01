<?php
namespace MtHash\Model\Asset;
use MtHash\Model\AbstractEntity;

class Algo extends AbstractEntity
{
    public $id, $name;
    public function initialize()
    {
        parent::initialize();
    }
}