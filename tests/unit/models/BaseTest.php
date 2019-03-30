<?php

namespace tests\unit\models;

use Codeception\Test\Unit;

class BaseTest extends Unit
{

    protected function _before()
    {
        parent::_before();
        $_SERVER['HTTP_HOST'] = 'localhost:8080';
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/html/web';
    }

    protected function _after()
    {
        parent::_after();
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['DOCUMENT_ROOT']);
    }

}