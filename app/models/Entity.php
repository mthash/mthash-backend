<?php
namespace MtHash\Model;
use Phalcon\Mvc\ModelInterface;

interface Entity extends ModelInterface
{
    public function createEntity (array $request) : Entity;
    public function editEntity (Entity $entity, array $request) : Entity;
    public function deleteEntity (Entity $entity) : bool;
}