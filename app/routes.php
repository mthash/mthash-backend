<?php
/**
 * @var \Phalcon\Mvc\Micro $app
 */

use MtHash\Controller\User\UserController;
use MtHash\Controller\User\WalletController;
use MtHash\Controller\User\AuthController;
use MtHash\Controller\Mining\TransactionController;
use MtHash\Controller\Dashboard\DashboardController;
use MtHash\Controller\Mining\ArcadeController;

$app->post('/user', [new UserController(), 'postCreate']);
$app->get ('/user/wallet', [new WalletController(), 'getList']);
$app->post ('/user/login', [new AuthController(), 'postLogin']);

$app->post ('/mining/{asset}/deposit', [new TransactionController(), 'postDeposit']);
$app->post ('/mining/{asset}/withdraw', [new TransactionController(), 'postWithdraw']);

$app->get ('/dashboard/info', [new DashboardController(), 'getOverviewStatistics']);

$app->post ('/transaction/free_deposit', [new \MtHash\Controller\Transaction\TransactionController(), 'postFreeDeposit']);
$app->post ('/transaction/exchange/{currency}', [new \MtHash\Controller\Transaction\TransactionController(), 'postExchange']);

$app->get ('/arcade', [new ArcadeController(), 'getInfo']);