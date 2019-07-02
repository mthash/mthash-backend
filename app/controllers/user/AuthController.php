<?php
namespace MtHash\Controller\User;
use MtHash\Controller\AbstractController;
use MtHash\Model\User\Jwt;
use MtHash\Model\User\UserRepository;

class AuthController extends AbstractController
{
    public function postLogin()
    {
        $request    = $this->getInput();
        $user       = UserRepository::byLogin($request['login']);

        if (!password_verify($request['password'], $user->password)) throw new \BusinessLogicException('Incorrect password');

        $tokenData  = $user->toArray (['id', 'name', 'login', 'created_at', 'status']);
        $tokenData['iat']   = time();

        $encodedTokenData   = Jwt::generate($tokenData);

        $this->webResponse($encodedTokenData);
    }
}