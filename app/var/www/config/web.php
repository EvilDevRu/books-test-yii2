<?php

use app\components\LocalFilesystemComponent;
use League\Glide\ServerFactory;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\CallableLocator;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use League\Tactician\Plugins\LockingMiddleware;
use yii\di\ServiceLocator;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'BRVg4vRMKhY1V768L3qDTb6V-D9fCe7K',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
        ],
        'fs' => [
            'class' => LocalFilesystemComponent::class,
            'path' => '@app/storage',
        ],
        'glide' => [
            'class' => \app\components\GlideComponent::class,
            'sourcePath' => '@app/storage',
            'cachePath' => '@webroot/cache/images',
            'baseUrl' => '/cache/images/',
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
                        \app\bus\BookSaveAuthorsCommand::class => \app\bus\BookSaveAuthorsHandler::class,
                        \app\bus\AuthorSubscribeCommand::class => \app\bus\AuthorSubscribeHandler::class,
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

            /**
             * Glide
             */
            \League\Glide\Server::class => function () {
                return ServerFactory::create([
                    'source' => Yii::getAlias('@app/storage'),
                    'cache' => Yii::getAlias('@webroot/cache/images'),

                    'driver' => 'imagick',
                    'max_image_size' => 2000 * 2000,
                    'defaults' => [
                        'q' => 80,
                        'fm' => 'jpg',
                    ],
                    'presets' => [
                        'small' => [
                            'w' => 200,
                            'h' => 200,
                            'fit' => 'crop',
                        ],
                        'medium' => [
                            'w' => 500,
                            'h' => 500,
                            'fit' => 'contain',
                        ],
                        'large' => [
                            'w' => 800,
                            'h' => 600,
                            'fit' => 'max',
                        ],
                        'thumbnail' => [
                            'w' => 100,
                            'h' => 100,
                            'fit' => 'crop',
                        ],
                    ],
                ]);
            },
        ],
    ],
    'params' => $params,
    'modules' => [],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20', '172.16.0.0/12']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20', '172.16.0.0/12']
    ];
}

return $config;
