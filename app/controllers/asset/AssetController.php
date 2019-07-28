<?php
namespace MtHash\Controller\Asset;
use MtHash\Controller\AbstractController;
use MtHash\Model\Asset\Asset;

class AssetController extends AbstractController
{
    public function getList()
    {
        $assets = Asset::find();
        $this->webResponse($assets);
    }
}