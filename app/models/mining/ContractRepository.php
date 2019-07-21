<?php
namespace MtHash\Model\Mining;
use MtHash\Model\User\User;

class ContractRepository
{
    static public function getUserInvestmentsPerAsset (User $user)
    {
        $request = '
            SELECT `asset_id`, SUM(`amount`) as `hash_invested`
            FROM `contract` 
            WHERE `status` > 0 and `user_id` = ' . $user->id . ' 
            GROUP by `user_id`
            HAVING SUM(`amount`) > 0
        ';

        $result     = \Phalcon\Di::getDefault()->get('db')->query ($request)->fetchAll (\PDO::FETCH_OBJ);
        return $result;
    }
}