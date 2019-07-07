<?php
namespace MtHash\Controller\Mining;
use MtHash\Controller\AbstractController;
use MtHash\Model\Asset\Asset;
use MtHash\Model\Mining\HASHContract;
use MtHash\Model\User\Wallet;

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

        return $this->webResponse($operation);
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

        return $this->webResponse($operation);
    }
}