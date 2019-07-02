<?php
namespace MtHash\Model\User;
class UserRepository
{
    static public function byLogin (string $login)
    {
        return User::failFindFirst (['status > 0 and login = ?0', 'bind' => [$login]]);
    }
}