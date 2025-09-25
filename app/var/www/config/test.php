<?php

use yii\helpers\ArrayHelper;

$config = ArrayHelper::merge(require(__DIR__ . '/app.php'), require(__DIR__ . '/console.php'), [
    'id' => 'test',
]);

return $config;
