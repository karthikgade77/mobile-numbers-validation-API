<?php

namespace app\modules\v1\models;

use app\modules\v1\components\Exceptions\ActiveRecordNotFoundException;
use app\modules\v1\components\Exceptions\SaveModelException;
use app\modules\v1\components\Utils\TimeUtils;
use app\modules\v1\components\Utils\Utils;
use Exception;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "File".
 *
 * @property int $id
 * @property string $created_at
 *
 * @property PhoneNumber[] $phoneNumbers
 */
class File extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoneNumbers(): ActiveQuery
    {
        return $this->hasMany(PhoneNumber::class, ['file_id' => 'id']);
    }

    public function beforeSave($insert) : bool
    {

        if ($insert) {
            $this->created_at = TimeUtils::now();
        }

        return parent::beforeSave($insert);
    }

    /**
     * Receives the file content as a string and returns an bidimensional array.
     *
     * @param string $file
     * @return array
     */
    public static function csvToArray(string $file)
    {
        $lines = explode(PHP_EOL, $file);
        $array = [];
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }
        return $array;
    }

    /**
     * Validates the file by iterating each line and validating each phone number.
     *
     * @param array $file array of content of the file
     * @return File object reference to the file
     * @throws Exception
     */
    public static function validateFile(array $file): File
    {

        if (empty($file)) {
            throw new InvalidArgumentException('Empty file');
        }

        $id = $file[0][0] ?? null;
        $sms_phone = $file[0][1] ?? null;

        if ($id !== 'id' && $sms_phone !== 'sms_phone') {
            throw new InvalidArgumentException('Malformed CSV');
        }

        $transaction = Yii::$app->getDb()->beginTransaction();

        try {

            $model = new File();

            if (!$model->save()) {
                throw new SaveModelException($model->getErrors());
            }

            //remove headers from csv
            unset($file[0]);

            foreach ($file as $key => $line) {

                $data = [];

                $fields = [
                    'identifier',
                    'number'
                ];

                for ($i = 0; $i < count($line); $i++) {
                    if (!empty($fields[$i])) {
                        $field_name = $fields[$i];
                        $data[$field_name] = isset($line[$i]) ? trim($line[$i]) : null;
                    }
                }

                if (empty($data['number'])) {
                    throw new InvalidArgumentException('Empty number on line ' . $key);
                }

                PhoneNumber::validateNumber($data['number'], $data['identifier'], $model->id);
            }

            $transaction->commit();

        } catch (Exception $e){
            $transaction->rollBack();
            throw $e;
        }


        return $model;
    }

    /**
     * Return stats array for the specific file.
     *
     * @param int $id
     * @return array
     * @throws ActiveRecordNotFoundException in case the file id is invalid
     */
    public static function getStats(int $id)
    {

        $file = File::findOne(['id' => $id]);

        if (empty($file)) {
            throw new ActiveRecordNotFoundException(File::class, $id);
        }

        $phone_numbers = $file->phoneNumbers;

        $stat_validated = 0;
        $stat_validated_with_fix = 0;
        $stat_invalidated = 0;

        foreach ($phone_numbers as $phone_number) {

            $validated = $phone_number->validated;
            $phone_number_fixes = $phone_number->phoneNumberFixes;

            if ($validated) {

                if (!empty($phone_number_fixes)) {
                    $stat_validated_with_fix++;
                } else {
                    $stat_validated++;
                }

            } else {
                $stat_invalidated++;
            }
        }

        $total = count($phone_numbers);

        //avoid division with 0
        $percentage_validated = $stat_validated != 0 ?
            round($stat_validated / $total * 100, 1) : 0;
        $percentage_validated_with_fix = $stat_validated_with_fix != 0 ?
            round($stat_validated_with_fix / $total * 100, 1) : 0;
        $percentage_invalidated = $stat_invalidated != 0 ?
            round($stat_invalidated / $total * 100, 1) : 0;

        return [
            'total' => $total,
            'validated' => $stat_validated,
            'invalidated' => $stat_invalidated,
            'validated_with_fix' => $stat_validated_with_fix,
            'percentage' => [
                'validated' => $percentage_validated,
                'invalidated' => $percentage_invalidated,
                'validated_with_fix' => $percentage_validated_with_fix,
            ]
        ];

    }

    /**
     * Returns the download link for the phone numbers that belong to the file.
     *
     * @param int $id
     * @return string download link
     * @throws ActiveRecordNotFoundException in case the file id is invalid
     */
    public static function getDownloadLink(int $id) : ?string
    {

        $file = File::findOne(['id' => $id]);

        if (empty($file)) {
            throw new ActiveRecordNotFoundException(File::class, $id);
        }

        $phone_numbers = $file->getPhoneNumbers()->all();

        if(empty($phone_numbers)){
            return null;
        }

        /** @var PhoneNumber $phone_number */
        foreach ($phone_numbers as &$phone_number) {
            $phone_number_fixes = $phone_number->getPhoneNumberFixes()->asArray()->all();

            $phone_number = $phone_number->toArray();
            $phone_number['fixes'] = $phone_number_fixes;
        }

        $file_path = 'files/phone_numbers_' . md5($file->id) . '.json';
        $file_path_absolute = $_SERVER['DOCUMENT_ROOT'] . '/' . $file_path;

        if (file_exists($file_path_absolute)) {
            return Utils::getBaseUrl() . $file_path;
        }

        self::createDownloadableFile($file_path, $phone_numbers);

        return Utils::getBaseUrl() . $file_path;

    }

    /**
     * Generate JSON file from array. Ignore if it's TEST Environment.
     *
     * @param string $file_path
     * @param array $data
     */
    public static function createDownloadableFile(string $file_path, array $data){

        //in tests we don't need to create the file
        if(YII_ENV_TEST){
            return;
        }

        if($fp = fopen($file_path, 'w')){
            fwrite($fp, json_encode($data));
            fclose($fp);
        }
    }
}