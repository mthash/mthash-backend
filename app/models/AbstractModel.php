<?php
namespace MtHash\Model;
use MtHash\Model\User\User;

class AbstractModel extends \Phalcon\Mvc\Model
{
    public function getUser() : User
    {
        return $this->getDI()->get('currentUser');
    }
}