<?php
/**
 * @var \Phalcon\Mvc\Micro $app
 */

use MtHash\Controller\User\UserController;
use MtHash\Controller\User\WalletController;
use MtHash\Controller\User\AuthController;
use MtHash\Controller\Mining\TransactionController;
use MtHash\Controller\Dashboard\DashboardController;
use MtHash\Controller\Mining\WidgetController;
use MtHash\Controller\Asset\AssetController;
use MtHash\Controller\Block\BlockController;
use MtHash\Controller\TestController;
use MtHash\Controller\Mining\ChartController;
use MtHash\Controller\OopsController;

// User
$app->post('/user', [new UserController(), 'postCreate']);
$app->get ('/user/wallet', [new WalletController(), 'getList']);
$app->post ('/user/login', [new AuthController(), 'postLogin']);
$app->post ('/demo/user/login/{sequence}', [new AuthController(), 'postDemoSpecifiedLogin']);
$app->post ('/demo/user/login', [new AuthController(), 'postDemoLogin']);
$app->get ('/demo/user', [new AuthController(), 'getListDemoUsers']);

// Mining
$app->post ('/mining/{asset}/deposit', [new TransactionController(), 'postDeposit']);
$app->post ('/mining/{asset}/withdraw', [new TransactionController(), 'postWithdraw']);
$app->get ('/mining/{asset}/predict', [new TransactionController(), 'getHashratePrediction']);
$app->get ('/mining/{asset}/maxes', [new TransactionController(), 'getMaxValues']);
$app->get ('/mining/stats', [new DashboardController(), 'getOverviewStatistics']);

// Transactions
$app->post ('/transaction/free_deposit', [new \MtHash\Controller\Transaction\TransactionController(), 'postFreeDeposit']);
$app->post ('/transaction/exchange/{currency}', [new \MtHash\Controller\Transaction\TransactionController(), 'postExchange']);

// Widget
$app->get ('/mining/arcade', [new WidgetController(), 'getArcadeBlock']);
$app->get ('/mining/portal', [new WidgetController(), 'getPortalBlock']);
$app->get ('/mining/rewards', [new BlockController(), 'getRewardsWidget']);
$app->get ('/mining/my/rewards', [new BlockController(), 'getMyRewardsWidget']);
$app->get ('/mining/arcade/hash', [new WidgetController(), 'getHashBalance']);


// Assets
$app->get ('/asset', [new AssetController(), 'getList']);
$app->get ('/asset/mineable', [new AssetController(), 'getMineable']);
$app->post ('/user/asset/{asset}', [new WidgetController(), 'postCreateAsset']);
$app->delete ('/user/asset/{asset}', [new WidgetController(), 'deleteAsset']);

// Chart
$app->get ('/mining/chart/{type}', [new ChartController(), 'getChart']);
$app->get ('/mining/chart', [new ChartController(), 'getChart']);

// Temporary
$app->get ('/oops/restart', [new OopsController(), 'getRestart']);


$app->get ('/test', [new TestController(), 'test']);
