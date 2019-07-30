<?php
namespace MtHash\Controller;
use MtHash\Model\AbstractEntity;
use MtHash\Model\User\User;
use Phalcon\Mvc\Controller;
use Valitron\Validator;

// Not really abstract for $app->error() correct work

/**
 * Class AbstractController
 * @package MtHash\Controller
 * @property User $currentUser
 */

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
    const   HTTP_UNPROCESSABLE      = 422;

    public function webResponse ($data, int $httpCode = self::HTTP_OK, ?string $message = null, ?string $trace = null)
    {
        $response['code']           = $httpCode;
        $response['body']           = $data;
        $response['message']        = $message;

        if (!empty ($trace)) $response['trace'] = $trace;

        if ($data instanceof \Throwable)
        {
            $response['code']           = self::HTTP_SERVER_ERROR;
            $response['body']           = null;
            $response['message']        = $data->getMessage();
            $response['trace']          = $data->getTraceAsString();

            if ($data instanceof \ValidationException)
            {
                $response['code']       = self::HTTP_UNPROCESSABLE;
            }
        }

        $this->response->setStatusCode($response['code']);
        $this->response->setContent('application/json');
        $this->response->setJsonContent($response);
        $this->response->send();
        exit;
    }

    public function getInput()
    {
        return $this->request->getJsonRawBody(true);
    }

    public function getFilter()
    {
        return $this->getUser()['filter'];
    }

    public function validateEntity (AbstractEntity $entity, $request = null) : void
    {
        if (empty ($request)) $request = $this->getInput();
        if (!isset ($entity->rules) || !is_array ($entity->rules)) return;

        $v              = new Validator($request);
        $v->mapFieldsRules($entity->rules);

        if (!$v->validate())
        {
            $this->webResponse($v->errors(), self::HTTP_UNPROCESSABLE, 'Validation Error');
        }
    }

    public function validateInput ($request, $rules)
    {
        if (empty ($request)) throw new \Exception('Empty request');
        if (count ($request) < 1) throw new \Exception('Empty request');

        $v              = new Validator($request);
        $v->mapFieldsRules($rules);

        if (!$v->validate())
        {
            $this->webResponse($v->errors(), self::HTTP_UNPROCESSABLE, 'Validation Error');
        }
    }

    public function getUser() : User
    {
        return $this->getDI()->get('currentUser');
    }
}