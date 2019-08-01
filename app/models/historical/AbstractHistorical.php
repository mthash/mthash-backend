<?php
namespace MtHash\Model\Historical;
use MtHash\Model\AbstractEntity;
use MtHash\Model\User\User;

class AbstractHistorical extends AbstractEntity
{
    const SECONDS_IN_DAY    = 3600 * 24;

    /**
     * @param User $user
     * @return \Phalcon\Mvc\Model|self|static
     */
    static public function changeToDay (User $user)
    {
        $record = self::findFirst (
            [
                'status > 0 and user_id = ?0 and created_at >= ?1',
                'bind'  => [$user->id, time() - self::SECONDS_IN_DAY],
                'order' => 'id DESC',
            ]
        );

        return $record;
    }
}