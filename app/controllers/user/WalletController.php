<?php
namespace MtHash\Controller\User;
use MtHash\Controller\AbstractController;
use MtHash\Model\User\Wallet;
use MtHash\Model\User\WalletRepository;

class WalletController extends AbstractController
{
    public function getList()
    {
        $wallets    = WalletRepository::byUser($this->currentUser);

        // Temporary
        if ($wallets->count() == 0)
        {
            (new Wallet())->createFor($this->currentUser);
            $wallets    = WalletRepository::byUser($this->currentUser);
        }


        $this->webResponse($wallets);
    }
}