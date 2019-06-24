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

    $app->handle();

} catch (\Throwable $e) {
      echo $e->getMessage() . '<br>';
      echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
