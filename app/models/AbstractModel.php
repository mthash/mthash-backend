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
        if (!$entity) throw new \BusinessLogicException('Such ' . (new \ReflectionClass(static::class))->getShortName() . ' does not exists');
        return $entity;
    }
}