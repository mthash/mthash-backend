<?php
namespace MtHash\Model\Historical;
use MtHash\Model\AbstractEntity;
use MtHash\Model\User\User;

class AbstractHistorical extends AbstractEntity
{
    const SECONDS_IN_DAY    = 3600 * 24;

    /**
     * @param User $user
     * @param Asset $asset
     * @return \Phalcon\Mvc\Model|self|static
     */
    static public function changeToDay (User $user, \MtHash\Model\Asset\Asset $asset)
    {
        $record = self::findFirst (
            [
                'status > 0 and user_id = ?0 and created_at >= ?1 and asset_id = ?2',
                'bind'  => [$user->id, time() - self::SECONDS_IN_DAY, $asset->id],
                'order' => 'id ASC',
            ]
        );

        return $record;
    }

    static public function generateStaticChart (int $number, string $period)
    {

    }
}