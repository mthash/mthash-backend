<?php
namespace MtHash\Controller\Asset;
use MtHash\Controller\AbstractController;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\AssetRepository;

class AssetController extends AbstractController
{
    public function getList()
    {
        $assets = Asset::find();
        $this->webResponse($assets);
    }

    public function getMineable()
    {
        $this->webResponse(AssetRepository::getMineable());
    }
}