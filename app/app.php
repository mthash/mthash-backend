<?php
/**
 * @var \Phalcon\Mvc\Micro $app
 */

use MtHash\Controller\AbstractController;

$app->get('/', function () {
    echo 'Hello world';
});

require_once ('routes.php');

$app->error(
    function (Throwable $exception) use ($app)
    {
        $code   = AbstractController::HTTP_SERVER_ERROR;
        $trace  = $body = null;

        if (true != getenv('IS_PRODUCTION'))
        {
            $trace = $exception->getTraceAsString();
        }

        if ($exception instanceof \BusinessLogicException)
        {
            $code = AbstractController::HTTP_BAD_REQUEST;
        }

        return $app->dispatcher->callActionMethod(
            new AbstractController(), 'webResponse',
            [
                $body, $code, $exception->getMessage(), $trace
            ]
        );
    }
);

$app->notFound(function () use($app) {
    return $app->dispatcher->callActionMethod(
        new AbstractController(), 'webResponse',
        [
            null, 404, 'Not Found',
        ]
    );
});
