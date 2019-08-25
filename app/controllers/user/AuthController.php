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

    public function postDemoSpecifiedLogin (string $tag)
    {
        $demoUser   = User::findFirst (
            [
                'tag = ?0', 'bind' => [$tag]
            ]
        );

        if (!$demoUser)
        {
            $demoUser   = (new User())->createDemo($tag);
        }

        $tokenData  = $demoUser->toArray(['id', 'name', 'login', 'created_at', 'status', 'tag']);
        $tokenData['iat']   = time();

        $encodedTokenData   = Jwt::generate($tokenData);
        $this->webResponse($encodedTokenData);
    }

    public function getListDemoUsers()
    {
        $demoUsers  = User::find (
            [
                'columns' => ['id', 'name', 'login', 'tag'],
                'status > 0 and is_demo = 1'
            ]
        );

        $this->webResponse($demoUsers);
    }
}