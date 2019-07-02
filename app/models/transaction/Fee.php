<?php
namespace MtHash\Model\Transaction;
class Fee
{
    private $mapping =
        [
            'p2p'                   =>
            [
                'frequency'                 => 'now',
                'amount'                    => 0.00,
                'percent'                   => 0.025,
            ],
            'start_mining'          =>
            [
                'frequency'                 => 'now',
                'amount'                    => 5,
                'currency'                  => 'HASH',
                'percent'                   => 0,
            ]
        ];

    private $originalAmount, $amountWithFee, $fee, $frequency, $recurringPeriod;

    public function __toString()
    {
        return (string) $this->fee;
    }

    public function getFrequency()
    {
        return $this->frequency;
    }

    public function getFee()
    {
        return $this->fee;
    }

    public function getAmountWithFee()
    {
        return $this->amountWithFee;
    }

    public function getOriginalAmount()
    {
        return $this->originalAmount;
    }

    public function getRecurringPeriod()
    {
        return $this->recurringPeriod;
    }


    public function __construct(string $feeName, float $amount)
    {
        if (!isset ($this->mapping[$feeName])) throw new \BusinessLogicException('Fee ' . $feeName . ' does not exists');

        $fee    = $this->mapping[$feeName];

        $this->originalAmount   = $amount;
        $this->fee              = $fee['amount'] + ($amount * $fee['percent']);
        $this->amountWithFee    = $amount + $this->fee;
        $this->frequency        = $fee['frequency'];
    }
}