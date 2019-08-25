<?php
namespace MtHash\Controller\Mining;
use MtHash\Controller\AbstractController;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Asset\AssetRepository;
use MtHash\Model\Asset\Units;
use MtHash\Model\Mining\ContractRepository;
use MtHash\Model\Mining\HASHContract;
use MtHash\Model\Mining\PortalDTO;
use MtHash\Model\User\Wallet;
use MtHash\Model\User\WalletRepository;

class TransactionController extends AbstractController
{
    public function postDeposit (string $asset)
    {
        $request    = $this->getInput();
        $this->validateInput($request, (new Request())->rules);

        $contract   = new HASHContract();
        $operation  = $contract->deposit(
            $this->getUser()->getWallet(),
            Asset::failFindFirst(['symbol = ?0', 'bind' => [$asset]]),
            $request['amount']
        );

        $response   = new PortalDTO($this->getUser());
        $this->webResponse($response->getAsset($asset));
    }

    public function getMaxValues (string $asset)
    {
        $asset  = AssetRepository::bySymbol($asset);

        $max    =
        [
            'deposit'       => WalletRepository::currencyByUser($this->getUser(), 'HASH')->balance,
            'withdraw'      => (new HASHContract())->getUserAllocatedTokens($asset)
        ];

        $this->webResponse($max);
    }

    public function getHashratePrediction (string $asset)
    {
        $request    = $this->request->getQuery();
        $this->validateInput($request, (new Request())->rules);

        $asset      = AssetRepository::bySymbol($asset);

        $contract   = new HASHContract();
        $tokensDep  = $contract->getUserAllocatedTokens($asset) + $request['amount'];
        $tokensWth  = $contract->getUserAllocatedTokens($asset) - $request['amount'];

        $this->webResponse(
            [
                'deposit'       => Units::pretty($contract->predictUserHashrate($asset, $tokensDep, true)),
                'withdraw'      => Units::pretty($contract->predictUserHashrate($asset, $tokensWth, false)),
            ]
        );
    }

    public function postWithdraw (string $asset)
    {
        $request    = $this->getInput();
        $this->validateInput($request, (new Request())->rules);

        $contract   = new HASHContract();
        $operation  = $contract->withdraw(
            $this->getUser()->getWallet(),
            Asset::failFindFirst(['symbol = ?0', 'bind' => [$asset]]),
            $request['amount']
        );

        $response   = new PortalDTO($this->getUser());
        $this->webResponse($response->getAsset($asset));

        return $this->webResponse($response);
    }
}