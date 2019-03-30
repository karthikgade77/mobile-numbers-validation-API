<?php

namespace app\modules\v1\components\Exceptions;

use Exception;

class SaveModelException extends Exception
{

    public function __construct(array $model_errors, int $code = 0, Exception $previous = null)
    {

        $message = "";

        foreach ($model_errors as $index => $msg) {
            $message = $msg[0];
            break;
        }

        parent::__construct($message, $code, $previous);
    }

}