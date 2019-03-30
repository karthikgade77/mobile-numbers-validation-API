<?php

$envs = [];
$envs['RELEASE'] = 'DEVELOPMENT';
$envs['YII_ENV'] = 'dev';
$envs['YII_DEBUG'] = true;

$required_envs = [
    'RELEASE',
    'YII_ENV',
    'YII_DEBUG',
    'ENV',
    'MYSQL_HOST',
    'MYSQL_DATABASE',
    'MYSQL_USER',
    'MYSQL_PASSWORD',
    'MYSQL_ROOT_PASSWORD',
    'TEST_MYSQL_HOST',
    'TEST_MYSQL_DATABASE',
    'TEST_MYSQL_USER',
    'TEST_MYSQL_PASSWORD',
    'TEST_MYSQL_ROOT_PASSWORD',
    'JWT_TOKEN'
];

foreach ($required_envs as $env) {
    $env_value = $envs[$env] ?? getenv($env) ?? null;

    if (is_null($env_value)) {
        throw new \Exception('Environment requires Env config "' . $env . '" to be set.');
    }

    defined($env) or define($env, $env_value);
}

# After we have declared all our constants, enable whatever necessary
if (YII_DEBUG) {
    // If we are debugging, force PHP to show errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}


