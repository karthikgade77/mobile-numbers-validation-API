<?php


use Codeception\Util\HttpCode;

/**
 * Class PhoneNumberCest
 *
 * @property string $token
 */
class PhoneNumberCest
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

    public function missingParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['random' => true]);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'MISSING_PARAMS']);
    }

    public function validateCorrectNumber(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => '27831234567']);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['data' => ['number' => '27831234567', 'validated' => true]]);
    }

    public function validateIncorrectNumber(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => '7831234567']);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();

        $expected = ['data' =>
            [
                'number' => '7831234567', 'validated' => false
            ]
        ];

        $apiTester->seeResponseContainsJson($expected);
    }

    public function validateCorrectNumberWithNonDigits(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => '278ahsadhjahjkhjk31  2345  67shjadajksdjkh']);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();

        $expected = [
            'data' => [
                'number' => '27831234567',
                'validated' => true,
                'fixes' => [
                    [
                        'fix_type' => 'REMOVE_NON_DIGITS',
                        'number_before' => '278ahsadhjahjkhjk31  2345  67shjadajksdjkh',
                        'number_after' => '27831234567'
                    ]
                ]
            ]
        ];

        $apiTester->seeResponseContainsJson($expected);
    }

    public function validateCorrectNumberWithNoCountryIndicative(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => '831234567']);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();

        $expected = [
            'data' => [
                'number' => '27831234567',
                'validated' => true,
                'fixes' => [
                    [
                        'fix_type' => 'ADD_COUNTRY_INDICATIVE',
                        'number_before' => '831234567',
                        'number_after' => '27831234567'
                    ]
                ]
            ]
        ];

        $apiTester->seeResponseContainsJson($expected);
    }

    public function validateCorrectNumberWithNonDigitsAndNoCountryIndicative(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Authorization', 'Bearer ' . $this->token);
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => ' 8ahsadhjahjkhjk31  2345  67shjadajksdjkh ']);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();

        $expected = [
            'data' => [
                'number' => '27831234567',
                'validated' => true,
                'fixes' => [
                    [
                        'fix_type' => 'REMOVE_NON_DIGITS',
                        'number_before' => ' 8ahsadhjahjkhjk31  2345  67shjadajksdjkh ',
                        'number_after' => '831234567'
                    ],
                    [
                        'fix_type' => 'ADD_COUNTRY_INDICATIVE',
                        'number_before' => '831234567',
                        'number_after' => '27831234567'
                    ]
                ]
            ]
        ];

        $apiTester->seeResponseContainsJson($expected);
    }

    public function validateWithoutToken(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('phone', ['number' => '27831234567']);
        $apiTester->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $apiTester->seeResponseIsJson();
    }


}
