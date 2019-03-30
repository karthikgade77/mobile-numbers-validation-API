<?php

namespace tests\unit\models;

use app\modules\v1\components\Exceptions\ActiveRecordNotFoundException;
use app\modules\v1\components\Utils\Utils;
use app\modules\v1\models\File;
use yii\base\InvalidArgumentException;

class FileTest extends BaseTest
{

    public function testValidateFileGetStatsAndGetDownloadLink(){

        $data = [
            ['id','sms_phone'],
            ['1','27831234561'],
            ['2','831234562'],
            ['3','831sadadaasda234dadsada5adasdadad63'],
            ['4','1213131312311314'],
        ];

        $file = File::validateFile($data)->toArray();
        $file['stats'] = File::getStats($file['id']);
        $file['download'] = File::getDownloadLink($file['id']);

        $expected['id'] = $file['id'];
        $expected['created_at'] = $file['created_at'];
        $expected['stats'] =  [
            'total' => 4,
            'validated' => 1,
            'invalidated' => 1,
            'validated_with_fix' => 2,
            'percentage' => [
                'validated' => 25,
                'invalidated' => 25,
                'validated_with_fix' => 50,
            ]
        ];
        $file_path = 'files/phone_numbers_' . md5($file['id']) . '.json';
        $expected['download'] = Utils::getBaseUrl() . $file_path;

        $this->assertEquals($expected, $file);

    }

    public function testValidateFileWithInts(){

        $data = [
            ['id','sms_phone'],
            [123,27831234567]
        ];

        $file = File::validateFile($data)->toArray();
        $file['stats'] = File::getStats($file['id']);

        $expected['id'] = $file['id'];
        $expected['created_at'] = $file['created_at'];
        $expected['stats'] =  [
            'total' => 1,
            'validated' => 1,
            'invalidated' => 0,
            'validated_with_fix' => 0,
            'percentage' => [
                'validated' => 100,
                'invalidated' => 0,
                'validated_with_fix' => 0,
            ]
        ];

        $this->assertEquals($expected, $file);

    }

    public function testValidateFileWithEmptyDigits(){

        $this->expectException(InvalidArgumentException::class);

        $data = [
            ['id','sms_phone'],
            ['','']
        ];

        $file = File::validateFile($data)->toArray();

    }

    public function testEmptyFile(){
        $this->expectException(InvalidArgumentException::class);

        $file = File::validateFile([])->toArray();
    }

    public function testFileWithInvalidHeaders(){

        $this->expectException(InvalidArgumentException::class);

        $data = [
            ['random_header1','random_header2'],
            ['123','27831234567']
        ];

        $file = File::validateFile([])->toArray();

    }

    public function testGetUnknownFileDetails(){

        $this->expectException(ActiveRecordNotFoundException::class);

        $stats = File::getStats(0);

    }

    public function testGetStatsFromEmptyFile(){

        $file = new File();
        $file->save();

        $stats = File::getStats($file->id);

        $expected_stats =  [
            'total' => 0,
            'validated' => 0,
            'invalidated' => 0,
            'validated_with_fix' => 0,
            'percentage' => [
                'validated' => 0,
                'invalidated' => 0,
                'validated_with_fix' => 0,
            ]
        ];

        $this->assertEquals($expected_stats, $stats);

    }

    public function testGetDownloadLinkFromEmptyFile(){

        $file = new File();
        $file->save();

        $link = File::getDownloadLink($file->id);

        $this->assertEquals(null, $link);

    }

    public function testValidateFileWithSameNumberTwoTimes(){

        $this->expectException(InvalidArgumentException::class);

        $data = [
            ['id','sms_phone'],
            ['1','27831234561'],
            ['2','831234562'],
            ['3','831sadadaasda134dadsada5adasdadad63'],
            ['4','27831234561'],
        ];

        $file = File::validateFile($data)->toArray();

    }

    public function testValidateFileWithSameIdentifierTwoTimes(){

        $this->expectException(InvalidArgumentException::class);

        $data = [
            ['id','sms_phone'],
            ['1','27831234567'],
            ['2','831234561'],
            ['3','831sadadaasda234dadsada5adasdadad62'],
            ['1','1213131312311312'],
        ];

        $file = File::validateFile($data)->toArray();

    }

}