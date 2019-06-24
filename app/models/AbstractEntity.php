<?php
namespace MtHash\Model;

abstract class AbstractEntity extends AbstractModel implements Entity
{
    use \Timestampable;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function createEntity(array $request): Entity
    {
        $this->fireEvent('mthash_entity:beforeCreate');
        $this->save ($request);
        $this->fireEvent('mthash_entity:afterCreate');
        return $this;
    }

    public function editEntity(Entity $entity, array $request): Entity
    {
        $entity->save ($request);
        return $entity;
    }

    public function deleteEntity(Entity $entity): bool
    {
        return $entity->save (['status' => -1]);
    }
}