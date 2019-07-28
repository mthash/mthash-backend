<?php
namespace MtHash\Model\Asset;
use Phalcon\Mvc\Model\ResultsetInterface;

class CMC
{
    const   QUOTES_LATEST_URL           = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
    const   API_KEY                     = '1bee11ab-efa6-43b2-9599-d349b2675949';

    private $response;

    /**
     * CMC constructor.
     * @param Asset[] $assets
     */
    public function __construct(ResultsetInterface $assets)
    {
        $symbols    = [];
        foreach ($assets as $asset)
        {
            $symbols[] = $asset->symbol;
        }

        $params['symbol']   = implode (',', $symbols);
        $headers            =
            [
                'Accepts: application/json',
                'X-CMC_PRO_API_KEY: ' . self::API_KEY
            ];

        $request    = self::QUOTES_LATEST_URL . '?' . http_build_query ($params);
        $handler    = curl_init();

        curl_setopt_array ($handler, [
            CURLOPT_URL             => $request,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => 1,
        ]);

        $this->response   = curl_exec ($handler);
        curl_close ($handler);
    }

    public function getResponse()
    {
        return json_decode ($this->response);
    }

}