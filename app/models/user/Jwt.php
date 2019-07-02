<?php
namespace MtHash\Model\User;
use \Firebase\JWT\JWT as JWTHandler;
use Phalcon\Http\Request;

class Jwt
{
    const ALGO              = 'HS256';
    const EXP_KEYWORD       = 'exp';
    const EXP_SECONDS       = 3600 * 24 * 365;
    const HEADER            = 'HTTP_MTHASH_AUTH';

    static public function sig() : string
    {
        /**
         * @var $request Request
         */
        $request    = \Phalcon\Di::getDefault()->get('request');

        return sha1 (
            $request->getClientAddress(true) . $request->getUserAgent() . $request->getBestLanguage() . $request->getBestAccept()
        );
    }

    static public function verifySig (?string $hash) : bool
    {
        /**
         * @var $request Request
         */
        $request    = \Phalcon\Di::getDefault()->get('request');

        return sha1 (
            $request->getClientAddress(true) . $request->getUserAgent() . $request->getBestLanguage() . $request->getBestAccept()
        ) === $hash;
    }

    static public function generate (array $data, ?string $secret = null)
    {
        if (is_null ($secret)) $secret = getenv('JWT_SECRET');

        $data[self::EXP_KEYWORD]    = time() + self::EXP_SECONDS;
        $data['for_testing']        = true;
        $data['sig']                = self::sig();

        return JWTHandler::encode ($data, $secret);
    }

    static public function fetch (string $jwt, ?string $secret = null, $algo = null)
    {
        $secret = is_null ($secret) ? getenv('JWT_SECRET') : $secret;
        $algo = is_null ($algo) ? self::ALGO : null;
        $algos[] = $algo;

        $decoded    = (array) JWTHandler::decode ($jwt, $secret, $algos);

        if ($decoded[self::EXP_KEYWORD] < time()) throw new \TokenException('Token is expired');
        if (true !== self::verifySig($decoded['sig'])) throw new \TokenException('Incorrect signature');

        return $decoded;
    }


}