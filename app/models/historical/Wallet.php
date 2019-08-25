<?php
namespace MtHash\Model\Historical;

use MtHash\Model\User\User;

class Wallet extends AbstractHistorical
{
    use \Timestampable;
    public function initialize()
    {
        parent::initialize();
        $this->setSource ('history_wallet');
    }

    /**
     * @param User $user
     * @param \MtHash\Model\User\Wallet $wallet
     * @return Wallet|self|static
     */
    static public function walletChangeToDay (User $user, \MtHash\Model\User\Wallet $wallet) : ?self
    {
        $record = self::findFirst (
            [
                'status > 0 and user_id = ?0 and created_at >= ?1 and wallet_id = ?2',
                'bind'  => [$user->id, time() - self::SECONDS_IN_DAY, $wallet->id],
                'order' => 'id ASC',
            ]
        );

        return !is_bool ($record) ? $record : null;
    }
}