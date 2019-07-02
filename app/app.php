<?php
/**
 * @var \Phalcon\Mvc\Micro $app
 */

$app->get('/', function () {
    echo 'Hello world';
});

require_once ('routes.php');

$app->error(
    function (Throwable $exception) use ($app)
    {
        $code   = \MtHash\Controller\AbstractController::HTTP_SERVER_ERROR;
        $trace  = $body = null;

        if (true !== getenv('IS_PRODUCTION'))
        {
            $trace = $exception->getTraceAsString();
        }

        if ($exception instanceof \BusinessLogicException)
        {
            $code = \MtHash\Controller\AbstractController::HTTP_BAD_REQUEST;
        }

        return $app->dispatcher->callActionMethod(
            new \MtHash\Controller\AbstractController(), 'webResponse',
            [
                $body, $code, $exception->getMessage(), $trace
            ]
        );
    }
);

$app->notFound(function () use($app) {
    return $app->dispatcher->callActionMethod(
        new \MtHash\Controller\AbstractController(), 'webResponse',
        [
            null, 404, 'Not Found',
        ]
    );
});
