<?php

namespace app\modules\v1\components\Utils;

class Utils
{

    public static function getBaseUrl(){
        return "http://" . $_SERVER['HTTP_HOST'] . '/';
    }

}