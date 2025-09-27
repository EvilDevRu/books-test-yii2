<?php

use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' => $_ENV['DB_MAIN_DSN'],
    'username' => $_ENV['DB_MAIN_USER'],
    'password' => $_ENV['DB_MAIN_PWD'],
    'charset' => 'utf8',
    'tablePrefix' => $_ENV['DB_MAIN_PREFIX'],

    'enableSchemaCache' => YII_ENV_PROD,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
