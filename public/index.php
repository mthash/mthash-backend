<?php
declare(strict_types=1);
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {
    require_once ('../vendor/autoload.php');
    $dotenv = \Dotenv\Dotenv::create(dirname (__DIR__));
    $dotenv->load();


    $di = new FactoryDefault();

    include APP_PATH . '/config/services.php';

    $config = $di->getConfig();

    include APP_PATH . '/config/loader.php';

    $app = new Micro($di);

    include APP_PATH . '/app.php';

    $app->handle(getenv('APP_URL'));

} catch (\Throwable $e) {
//    echo '<pre>';
//    print_r($e);
//    echo '</pre>';
//    die();
    $response['code']   = 500;
    $response['body']   = $e->getMessage();
    $app->response->setStatusCode($response['code']);
    $app->response->setContent('application/json');
    $app->response->setJsonContent($response);
    $app->response->send();
}
