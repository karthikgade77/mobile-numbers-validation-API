<?php


use Codeception\Util\HttpCode;

class AuthCest
{
    public function authCredential(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('auth', ['username' => 'username', 'password' => 'password']);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();
        //token will vary
        $apiTester->seeResponseContainsJson(['type' => 'AUTH_SUCCESS']);
    }

    public function authBadCredential(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('auth', ['username' => 'username_false', 'password' => 'password_false']);
        $apiTester->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'UNAUTHORIZED_CREDENTIAL']);
    }

    public function missingParams(ApiTester $apiTester)
    {
        $apiTester->haveHttpHeader('Content-Type', 'application/json');
        $apiTester->sendPOST('auth', ['false_param' => true]);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['type' => 'MISSING_PARAMS']);
    }
}
