<?php
namespace MtHash\Model\Mining;
class RewardFilter
{
    private $request    = '';
    private $bind       = [];
    private $keys       = ['asset_id'];

    public function __construct(string $request, array $filter)
    {
        $postRequest    = '';
        $params         = [];

        foreach ($filter as $filterKey => $filterValue)
        {
            if (!in_array ($filterKey, $this->keys)) throw new \BusinessLogicException('Filter ' .$filterKey . ' not available here');
            $postRequest.= ' and ' . $filterKey . ' = ?' . count ($params);
            $params[] = $filterValue;
        }

        $this->request  = $request . $postRequest;
        $this->bind     = $params;
    }

    public function getRequest() : string
    {
        return $this->request;
    }

    public function getBind() : array
    {
        return $this->bind;
    }
}