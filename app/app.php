<?php
/**
 * @var \Phalcon\Mvc\Micro $app
 */

$app->get('/', function () {
    echo 'Hello world';
});

$app->post('/user', [new \MtHash\Controller\User\UserController(), 'postCreate']);

$app->error(
    function (Throwable $exception) use ($app)
    {
        $code   = \MtHash\Controller\AbstractController::HTTP_SERVER_ERROR;
        $trace  = '';

        if (true !== getenv('IS_PRODUCTION'))
        {
            $trace = $exception->getTraceAsString();
        }

        return $app->dispatcher->callActionMethod(
            new \MtHash\Controller\AbstractController(), 'webResponse',
            [
                null, $code, $exception->getMessage() . "\n" . $trace
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
