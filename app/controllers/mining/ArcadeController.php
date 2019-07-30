<?php
namespace MtHash\Controller\Mining;
use MtHash\Controller\AbstractController;
use MtHash\Model\Mining\Contract;
use MtHash\Model\Mining\ContractDTO;
use MtHash\Model\Mining\ContractRepository;

class ArcadeController extends AbstractController
{
    public function getInfo()
    {
        $response   = new ContractDTO($this->getUser());
        $response->setData (ContractRepository::getUserInvestmentsPerAsset($this->getUser()));

        $this->webResponse($response->getAssets());
    }

    public function getHashBalance()
    {
        $response   = ContractRepository::getUserInvestedHash ($this->getUser());
        $this->webResponse($response);
    }
}