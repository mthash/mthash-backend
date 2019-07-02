<?php
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

require_once (APP_PATH . '/../vendor/autoload.php');
$dotenv = \Dotenv\Dotenv::create(dirname (dirname (__DIR__)));
$dotenv->load();

if (getenv('IS_PRODUCTION') === true)
{
    error_reporting (0);
    ini_set ('display_errors', 'off');
}
else
{
    error_reporting(E_ALL | E_STRICT);
    ini_set ('display_errors', 'on');
}

ini_set ('phalcon.orm.cast_on_hydrate', 'on');

return new \Phalcon\Config([
    'database' => [
        'adapter'    => 'Mysql',
        'host'       => getenv('DB_HOST'),
        'username'   => getenv('DB_USR'),
        'password'   => getenv('DB_PWD'),
        'dbname'     => getenv('DB_NAME'),
        'charset'    => 'utf8',
    ],

    'application' => [
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'baseUri'        => 'http://dev.api.mthash.com',
    ]
]);
