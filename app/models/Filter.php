<?php
namespace MtHash\Model;
class Filter
{
    private $request    = '';
    private $bind       = [];
    private $keys       = [];

    public function __construct(string $request, array $filter, ?array $keys = [])
    {
        $postRequest    = '';
        $params         = [];
        if (count ($keys) == 0) $keys = $this->keys;

        foreach ($filter as $filterKey => $filterValue)
        {
            if (is_null ($filterValue)) continue;
            $operation  = '=';

            if (!in_array ($filterKey, $keys)) throw new \BusinessLogicException('Filter ' .$filterKey . ' not available here');
            if (is_array ($filterValue))
            {
                list ($operation, $filterValue) = $filterValue;
            }



            $postRequest.= ' and ' . $filterKey . ' ' . $operation . ' ?' . count ($params);
            $params[] = $filterValue;
        }

        $this->request  = $request . $postRequest;
        $this->bind     = $params;
    }

    public function setPossibleKeys (array $keys)
    {
        $this->keys = $keys;
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