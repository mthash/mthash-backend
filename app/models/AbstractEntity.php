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
        $this->fireEvent('mthash_entity:beforeUpdate');
        $entity->save ($request);
        $this->fireEvent('mthash_entity:afterUpdate');
        return $entity;
    }

    public function deleteEntity(Entity $entity): bool
    {
        return $entity->save (['status' => -1]);
    }

    static public function failFindFirst ($parameters = null)
    {
        $entity = parent::findFirst ($parameters);
        if (!$entity) throw new \BusinessLogicException('Such ' . (new \ReflectionClass(static::class))->getShortName() . ' does not exists');
        return $entity;
    }
}