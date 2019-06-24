<?php
namespace MtHash\Controller;
use Phalcon\Mvc\Controller;

// Not really abstract for $app->error() correct work
class AbstractController extends Controller
{
    const   HTTP_OK                 = 200;
    const   HTTP_SERVER_ERROR       = 500;
    const   HTTP_NOT_FOUND          = 404;
    const   HTTP_NO_CONTENT         = 204;
    const   HTTP_BAD_REQUEST        = 400;
    const   HTTP_UNAUTHORIZED       = 401;
    const   HTTP_FORBIDDEN          = 403;
    const   HTTP_CREATED            = 201;

    public function webResponse ($data, int $httpCode = self::HTTP_OK, ?string $message = null)
    {
        $response['code']           = $httpCode;
        $response['body']           = $data;
        $response['message']        = $message;

        if ($data instanceof \Throwable)
        {
            $data['code']           = self::HTTP_SERVER_ERROR;
            $data['body']           = null;
            $data['message']        = $data->getMessage();
            $data['trace']          = $data->getTraceAsString();
        }

        $this->response->setContent('application/json');
        $this->response->setJsonContent($response);
        $this->response->send();
        exit;
    }

    public function getInput()
    {
        return $this->request->getJsonRawBody(true);
    }
}