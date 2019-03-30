<?php

require_once('load_env.php');

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . getenv('TEST_MYSQL_HOST') . ';dbname=' . getenv('TEST_MYSQL_DATABASE'),
    'username' => getenv('TEST_MYSQL_USER'),
    'password' => getenv('TEST_MYSQL_PASSWORD'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
