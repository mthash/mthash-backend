<?php
$loader = new \Phalcon\Loader();
$namespaces = [];

foreach (['user', 'wallet'] as $entity)
{
    $namespaces['MtHash\Model\\' . ucfirst ($entity)]           = APP_PATH . '/models/' . $entity . '/';
    $namespaces['MtHash\Controller\\' . ucfirst ($entity)]      = APP_PATH . '/controllers/' . $entity . '/';
}

$namespaces['MtHash\Model']         = APP_PATH . '/models/';
$namespaces['MtHash\Controller']    = APP_PATH . '/controllers/';

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/traits/',
    ]
);

$loader->registerNamespaces($namespaces);
$loader->register();
