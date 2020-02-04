<?php
namespace MtHash\Controller\Mining;
use MtHash\Controller\AbstractController;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\AssetRepository;
use MtHash\Model\Mining\Contract;
use MtHash\Model\Mining\ContractDTO;
use MtHash\Model\Mining\ContractRepository;
use MtHash\Model\Mining\HASHContract;
use MtHash\Model\Mining\PortalDTO;
use MtHash\Model\User\Wallet;
use MtHash\Model\User\WalletRepository;

class WidgetController extends AbstractController
{
    public function getArcadeBlock()
    {
        $response   = new ContractDTO($this->getUser());
        $response->setData (ContractRepository::getUserInvestmentsPerAsset($this->getUser()));

        $this->webResponse($response->getAssets());
    }

    public function getPortalBlock()
    {
        $response   = new PortalDTO($this->getUser());
        $this->webResponse($response->getAssets());
    }

    public function getHashBalance()
    {
        $response   = WalletRepository::getHashBalanceWithChange($this->getUser());
        $this->webResponse($response);
    }

    public function postCreateAsset (string $asset)
    {
        $asset      = \MtHash\Model\User\AssetRepository::find ($this->getUser(), AssetRepository::bySymbol($asset));
        $asset->makeVisible();

        $response   = new PortalDTO($this->getUser());

        $this->webResponse($response->getAsset($asset->asset->symbol));
    }

    public function deleteAsset (string $asset)
    {
        $asset      = \MtHash\Model\User\AssetRepository::find ($this->getUser(), AssetRepository::bySymbol($asset));
        $asset->makeInvisible();

        // Withdraw
        $contract   = new HASHContract();
        $operation  = $contract->withdraw(
            $this->getUser()->getWallet(),
            Asset::failFindFirst(['symbol = ?0', 'bind' => [$asset]])
        );

        $response   = new PortalDTO($this->getUser());

        $this->webResponse($response->getAsset($asset->asset->symbol));
    }
}