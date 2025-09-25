<?php

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\CallableLocator;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use League\Tactician\Plugins\LockingMiddleware;
use yii\di\ServiceLocator;
use yii\db\Connection;

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'app',
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
    ],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:[\w\-]+>/<controller:[\w\-]+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:[\w\-]+>/<controller:[\w\-]+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:[\w\-]+>/<id:\d+>' => '<controller>/view',
                '<controller:[\w\-]+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'db' => [
            'class' => Connection::class,
            'dsn' => $_ENV['DB_MAIN_DSN'],
            'username' => $_ENV['DB_MAIN_USER'],
            'password' => $_ENV['DB_MAIN_PWD'],
            'charset' => 'utf8',
            'tablePrefix' => $_ENV['DB_MAIN_PREFIX'],

            'enableSchemaCache' => YII_ENV_PROD,
            'schemaCacheDuration' => 60,
            'schemaCache' => 'cache',
        ],
    ],
    'container' => [
        'definitions' => [
            /**
             * Командная шина.
             */
            CommandBus::class => static function () {
                $locator = new ServiceLocator([
                    'components' => [
                        //  todo
                    ],
                ]);

                $lockingMiddleware = new LockingMiddleware();
                $commandMiddleware = new CommandHandlerMiddleware(
                    new ClassNameExtractor(),
                    new CallableLocator([$locator, 'get']),
                    new InvokeInflector()
                );

                return new CommandBus([$lockingMiddleware, $commandMiddleware]);
            },
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        'allowedIPs' => ['*'],
    ];
}

return $config;
