<?php
namespace MtHash\Controller\User;
use MtHash\Controller\AbstractController;
use MtHash\Model\User\Jwt;
use MtHash\Model\User\User;
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

    public function postDemoLogin()
    {
        $demoUser   = User::findFirst(['login = ?0', 'bind' => ['demo@mthash.com']]);
        if (!$demoUser)
        {
            $demoUser   = (new \SeederTask())->usersAction();
        }

        $tokenData  = $demoUser->toArray(['id', 'name', 'login', 'created_at', 'status']);
        $tokenData['iat']   = time();

        $encodedTokenData   = Jwt::generate($tokenData);
        $this->webResponse($encodedTokenData);
    }

    public function postDemoSpecifiedLogin (int $demoSequence)
    {
        $demoUser   = User::findFirst (
            [
                'login = ?0', 'bind' => ['demo' . $demoSequence . '@mthash.com']
            ]
        );

        if (!$demoUser)
        {
            $demoUser   = (new User())->createDemo($demoSequence);
        }

        $tokenData  = $demoUser->toArray(['id', 'name', 'login', 'created_at', 'status']);
        $tokenData['iat']   = time();

        $encodedTokenData   = Jwt::generate($tokenData);
        $this->webResponse($encodedTokenData);
    }

    public function getListDemoUsers()
    {
        $demoUsers  = User::find (
            [
                'status > 0 and is_demo = 1'
            ]
        );

        $this->webResponse($demoUsers->toArray(['id', 'name', 'login']));
    }
}