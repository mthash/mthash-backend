<?php
namespace MtHash\Model\Mining\Widget;
use MtHash\Model\Mining\Block;
use MtHash\Model\Transaction\Transaction;
use MtHash\Model\Transaction\Type;
use MtHash\Model\User\User;

class MyRewardsDTO
{
    private $age, $coin, $percent_reward, $amount_reward, $fee, $earnings;

    public function __construct (Block $block, User $user)
    {
        $transaction    = Transaction::findFirst(
            [
                'status > 0 and to_user_id = ?0 and block_id = ?1 and type_id = ?2',
                'bind'  => [$user->id, $block->id, Type::MINING]
            ]
        );

        if (!$transaction) return;

        $this->age              = $block->created_at;
        $this->coin             = $block->asset->symbol;
        $this->percent_reward   = $transaction->percent;
        $this->amount_reward    = $transaction->amount;
        $this->fee              = 0;
        $this->earnings         = $this->amount_reward - $this->fee;
    }

    public function fetch() : array
    {
        if (empty ($this->age)) return [];

        return
        [
            'age'               => $this->age,
            'coin'              => $this->coin,
            'percent_reward'    => $this->percent_reward,
            'amount_reward'     => $this->amount_reward,
            'fee'               => $this->fee,
            'earnings'          => $this->earnings,
        ];
    }
}