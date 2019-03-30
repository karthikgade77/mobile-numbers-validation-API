<?php

namespace tests\unit\models;

use app\modules\v1\components\Exceptions\ActiveRecordNotFoundException;
use app\modules\v1\models\PhoneNumber;
use yii\base\InvalidArgumentException;

class PhoneNumberTest extends BaseTest
{

    public function testValidateEmptyNumber(){
        $this->expectException(InvalidArgumentException::class);
        $result = PhoneNumber::validateNumber('');
    }

    public function testValidateInvalidFileId(){
        $this->expectException(ActiveRecordNotFoundException::class);
        $result = PhoneNumber::validateNumber('27831234567',null,999999999);
    }

    public function testValidateCorrectNumberWithIdentifier()
    {
        $phone_number = '27831234567';
        $identifier = 9999;

        $result = PhoneNumber::validateNumber($phone_number,$identifier);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = $identifier;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateCorrectNumberWithNoCountryIndicative()
    {
        $phone_number = '831234567';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateCorrectNumberWithNonDigits()
    {
        $phone_number = '278ahsadhjahjkhjk31  2345  67shjadajksdjkh';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateCorrectNumberWithNonDigitsAndNoCountryIndicative()
    {
        $phone_number = ' 8ahsadhjahjkhjk31  2345  67shjadajksdjkh ';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '27831234567';
        $expected->validated = true;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateIncorrectNumberWithoutFirstIndicativeNumber()
    {
        $phone_number = '7831234567';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '7831234567';
        $expected->validated = false;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateIncorrectNumberWithInvalidLengthAndNonDigits()
    {
        $phone_number = '_DELETED_1234567';

        $result = PhoneNumber::validateNumber($phone_number);

        $expected = new PhoneNumber();
        $expected->id = $result->id;
        $expected->identifier = null;
        $expected->file_id = null;
        $expected->number = '_DELETED_1234567';
        $expected->validated = false;
        $expected->created_at = $result->created_at;

        $this->assertEquals($expected->getAttributes(), $result->getAttributes());
    }

    public function testValidateSameIdentifierTwoTimes(){

        $this->expectException(InvalidArgumentException::class);

        $result = PhoneNumber::validateNumber('27831234567',9999);
        $result = PhoneNumber::validateNumber('27831234517',9999);

    }

    public function testValidateSameNumberTwoTimes(){

        $this->expectException(InvalidArgumentException::class);

        $result = PhoneNumber::validateNumber('27831234567');
        $result = PhoneNumber::validateNumber('27831234567');

    }

}