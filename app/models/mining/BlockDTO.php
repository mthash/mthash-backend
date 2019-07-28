<?php
namespace MtHash\Model\Mining;
class BlockDTO
{
    private $age, $coin, $height, $reward, $fee, $hash;

    public function __construct (Block $block)
    {
        $this->age      = $block->created_at;
        $this->coin     = $block->asset->symbol;
        $this->height   = $block->id;
        $this->reward   = $block->reward;
        $this->fee      = 0.00;
        $this->hash     = $block->hash;
    }

    public function fetch()
    {
        return
        [
            'age'               => $this->age,
            'coin'              => $this->coin,
            'height'            => $this->height,
            'reward'            => $this->reward,
            'fee'               => $this->fee,
            'hash'              => '0x' . $this->hash,
        ];
    }
}