<?php
namespace MtHash\Controller\Transaction;
use MtHash\Controller\AbstractController;
use MtHash\Model\Transaction\Transaction;
use MtHash\Model\Asset\Asset;
use MtHash\Model\User\Wallet;
use MtHash\Model\User\WalletRepository;

class TransactionController extends AbstractController
{
    public function postFreeDeposit()
    {
        $input  = $this->getInput();

        $asset  = Asset::failFindFirst($input['asset_id']);

        $transaction    = new Transaction();
        $response       = $transaction->freeDeposit(
            $asset, WalletRepository::byUserWithAsset($this->getUser(), $asset), $input['amount']
        );

        return $this->webResponse($response);
    }

    public function postExchange(string $currency)
    {
        $input          = $this->getInput();
        $transaction    = new Transaction();
        $wallet         = Wallet::failFindFirst($input['wallet_id']);

        if ($wallet->user_id != $this->getUser()->id) throw new \BusinessLogicException('You are not the owner of the wallet');

        $response       = $transaction->exchange (
            $wallet, Asset::failFindFirst(['symbol = ?0', 'bind' => [$currency]]), $input['amount']
        );

        return $this->webResponse($response);
    }
}