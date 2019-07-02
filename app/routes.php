<?php
/**
 * @var \Phalcon\Mvc\Micro $app
 */

$app->post('/user', [new \MtHash\Controller\User\UserController(), 'postCreate']);
$app->get ('/user/wallet', [new \MtHash\Controller\User\WalletController(), 'getList']);
$app->post ('/user/login', [new \MtHash\Controller\User\AuthController(), 'postLogin']);