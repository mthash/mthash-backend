<?php
namespace MtHash\Model;
use MtHash\Model\User\User;

class AbstractModel extends \Phalcon\Mvc\Model
{
    public function getUser() : User
    {
        return $this->getDI()->get('currentUser');
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model|self|static
     * @throws \BusinessLogicException
     * @throws \ReflectionException
     */
    static public function failFindFirst ($parameters = null)
    {
        $entity = parent::findFirst ($parameters);
        if (!$entity)
        {
            $message    = 'Such ' . (new \ReflectionClass(static::class))->getShortName() . ' does not exists';
            if (false == getenv('IS_PRODUCTION')) $message.= print_r ($parameters, 1);
            throw new \BusinessLogicException($message);
        }
        return $entity;
    }

    /**
     * @param null $parameters
     * @return \Phalcon\Mvc\Model\ResultsetInterface|void|static[]|self[]
     */
    static public function find($parameters = null)
    {
        return parent::find($parameters);
    }
}