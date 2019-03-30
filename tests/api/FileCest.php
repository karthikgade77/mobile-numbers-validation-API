<?php

use Codeception\Util\HttpCode;


/**
 * Class FileCest
 *
 * @property string $token
 */
class FileCest
{
    private $token;

    public function _before(ApiTester $apiTester)
    {

        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('auth', ['username' => 'username', 'password' => 'password']);
        $response = json_decode($apiTester->grabResponse(), true);
        $this->token = $response['data']['jwt'] ?? null;

        if (empty($this->token)) {
            throw new UnexpectedValueException('Token must not be null');
        }

    }

    public function validateFileWithSameNumberTwoTimes(ApiTester $apiTester){
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendPOST('file', 'id,sms_phone
1,27831234567
2,831234561
3,831sadadaasda234dadsada5adasdadad62
4,27831234567');
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
    }

    public function validateFileWithSameIdentifierTwoTimes(ApiTester $apiTester){
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendPOST('file', 'id,sms_phone
1,27831234567
2,831234561
3,831sadadaasda234dadsada5adasdadad62
1,1213131312311312');
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
    }

    public function validateFileAndGetStats(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendPOST('file', 'id,sms_phone
1,27831234567
2,831234561
3,831sadadaasda234dadsada5adasdadad62
4,1213131312311312');
        $response = json_decode($apiTester->grabResponse(),true);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();

        $file_id = $response['data']['id'] ?? null;

        if (empty($file_id)) {
            throw new UnexpectedValueException('File Id must not be null');
        }

        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendGET('file/' . $file_id);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();

        $expected = [
            'data' => [
                'stats' => [
                    'total' => 4,
                    'validated' => 1,
                    'invalidated' => 1,
                    'validated_with_fix' => 2,
                    'percentage' => [
                        'validated' => 25,
                        'invalidated' => 25,
                        'validated_with_fix' => 50,
                    ]
                ]
            ]
        ];

        $apiTester->seeResponseContainsJson($expected);
    }

    public function validateFileMissingParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendPOST('file', null);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'MISSING_PARAMS']);
    }

    public function getFileStatsMissingParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendGET('file');
        $apiTester->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $apiTester->seeResponseIsJson();
    }

    public function getFileStatsInvalidId(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->sendGET('file/999999999');
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'INVALID_ID']);

    }
}
