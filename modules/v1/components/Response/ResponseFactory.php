<?php

namespace app\modules\v1\components\Response;

use Yii;

class ResponseFactory
{
    /**
     * @param array | object $data
     * @param string $type
     * @param string $message
     * @param int $http_status
     * @return array
     */
    public function success(array $data = [],
                            string $type = 'NO_TYPE_PROVIDED',
                            string $message = 'No message provided.',
                            int $http_status = HttpStatus::HTTP_OK
    )
    {



        Yii::$app->getResponse()->setStatusCode($http_status);

        return [
            'type' => $type,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function false(?array $data = [],
                          string $type = 'NO_TYPE_PROVIDED',
                          string $message = 'No message provided.',
                          int $http_status = HttpStatus::HTTP_BAD_REQUEST)
    {
        Yii::$app->getResponse()->setStatusCode($http_status);

        return [
            'type' => $type,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function falseAccessDenied(array $data = [],
                                      string $type = 'UNAUTHORIZED_CREDENTIAL',
                                      string $message = 'No message provided.')
    {
        return $this->false($data, $type, $message, HttpStatus::HTTP_UNAUTHORIZED);
    }

    public function falseServerError(array $data = [],
                                     string $type = 'INTERNAL_SERVER_ERROR',
                                     string $message = 'No message provided.')
    {
        return $this->false($data, $type, $message, HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function falseMissingParams(array $data = [],
                                       string $type = 'MISSING_PARAMS',
                                       string $message = 'No message provided.')
    {
        return $this->false($data, $type, $message);
    }

}