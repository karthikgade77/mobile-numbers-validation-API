<?php

namespace app\modules\v1\components\Utils;

use DateTime;

class TimeUtils
{

    public static function now()
    {
        return (new DateTime())->format('Y-m-d H:i:s');
    }

}