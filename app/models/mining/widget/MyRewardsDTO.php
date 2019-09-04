<?php
namespace MtHash\Model\Mining\Widget;
use MtHash\Model\Mining\Block;
use MtHash\Model\Transaction\Transaction;
use MtHash\Model\Transaction\Type;
use MtHash\Model\User\User;

class MyRewardsDTO
{
    private $blocks = [];

    public function __construct (User $user)
    {
        $transactions   = Transaction::find (
            [
                'status > 0 and to_user_id = ?0 and type_id = ?1 and created_at > ?2',
                'bind' => [$user->id, Type::MINING, time() - 3600]
            ]
        );

        if (!$transactions) return;

        foreach ($transactions as $transaction)
        {
            $block  =
                [
                    'age'               => $transaction->block->created_at,
                    'coin'              => $transaction->block->asset->symbol,
                    'percent_reward'    => $transaction->percent,
                    'amount_reward'     => $transaction->amount,
                    'fee'               => 0,
                    'earnings'          => $transaction->amount,
                ];

            $this->blocks[] = $block;
        }
    }

    public function fetch() : array
    {
        return $this->blocks;
    }
}