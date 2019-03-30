<?php

namespace tests\unit\models;


use app\modules\v1\models\PhoneNumberFix;

class PhoneNumberFixTest extends BaseTest
{

    public function testRemoveNonDigits()
    {
        $phone_number = '\|+*27831qweadas234_!  §567';

        $result = PhoneNumberFix::removeNonDigits($phone_number);

        $this->assertEquals('27831234567', $result);
    }

    public function testAddCountryIndicativeToValidNumber()
    {
        $phone_number = '831234567';

        $result = PhoneNumberFix::addCountryIndicative($phone_number);

        $this->assertEquals('27831234567', $result);
    }

    public function testAddCountryIndicativeToInvalidNumber()
    {
        $phone_number = 'dsadasjfaksdadadjkj 831234 ejqkwkqkeqeqwjk 567 eqwejqkqqeqkjeqlk';

        $result = PhoneNumberFix::addCountryIndicative($phone_number);

        $this->assertEquals('dsadasjfaksdadadjkj 831234 ejqkwkqkeqeqwjk 567 eqwejqkqqeqkjeqlk', $result);
    }

}

