<?php
namespace MtHash\Model\Mining;
class SharesDTO
{
    private $users          = [];
    private $totalShares = 0;

    public function addUser (int $userId, int $shares) : void
    {
        $this->users[$userId]   = $shares;
        $this->totalShares     += $shares;
    }

    public function getUserShare (int $userId) : int
    {
        return $this->users[$userId] ?? 0;
    }

    public function getTotalShares() : int
    {
        return $this->totalShares;
    }

    public function getUsers() : array
    {
        return $this->users;
    }





}