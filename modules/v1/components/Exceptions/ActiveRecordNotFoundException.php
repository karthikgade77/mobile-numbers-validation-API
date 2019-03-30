<?php

namespace app\modules\v1\components\Exceptions;

use Exception;

class ActiveRecordNotFoundException extends Exception
{

    public function __construct(string $active_record, int $record_id = null, int $code = 0, Exception $previous = null)
    {

        $message = $active_record . ' not found';

        if(!empty($record_id)){
            $message .= ' for id: ' . $record_id;
        }

        parent::__construct($message, $code, $previous);
    }

}