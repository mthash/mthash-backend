<?php
namespace MtHash\Controller\User;
use MtHash\Controller\AbstractController;
use MtHash\Model\User\User;
use MtHash\Model\User\Wallet;

class UserController extends AbstractController
{
    public function postCreate()
    {
        $user           = new User();
        $createdUser    = $user->createEntity($this->getInput());

        if ($createdUser instanceof User)
        {
            $wallet = new Wallet();
            $wallet->createFor($createdUser);
        }

        $this->webResponse($createdUser, self::HTTP_CREATED);
    }
}