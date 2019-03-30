<?php

namespace app\modules\v1\controllers;

use app\modules\v1\components\Exceptions\ActiveRecordNotFoundException;
use app\modules\v1\models\File;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\Exception;

class FileController extends BaseController
{

    /**
     * Endpoint to get file details by id. It returns the file_id, stats and download link for
     * the phone numbers validated.
     * @return array
     * @throws ActiveRecordNotFoundException
     */
    public function actionDetails()
    {
        $id = $this->getQuery('id');

        if(empty($id)){
            return $this->response->falseMissingParams();
        }

        try{
            $file = File::findOne(['id' => $id]);

            if(empty($file)){
                throw new ActiveRecordNotFoundException(File::class, $id);
            }

            $file = $file->toArray();
            $file['stats'] = File::getStats($file['id']);
            $file['download'] = File::getDownloadLink($file['id']);

        } catch (ActiveRecordNotFoundException $e){
            return $this->getResponse()->false([],'INVALID_ID');
        } catch( Exception $e){
            return $this->getResponse()->falseServerError();
        }

        return $this->getResponse()->success($file);
    }

    /**
     * Endpoint that receives a binary file (csv) and validates each phone number.
     * It returns the file_id, stats and download link for the phone numbers validated.
     *
     * @return array
     */
    public function actionValidate()
    {
        $file = $this->getRawBody();

        if(empty($file)){
            return $this->response->falseMissingParams();
        }

        try {
            $file = File::csvToArray($file);
            $file = File::validateFile($file)->toArray();
            $file['stats'] = File::getStats($file['id']);
            $file['download'] = File::getDownloadLink($file['id']);
        } catch(InvalidArgumentException $e){
            return $this->getResponse()->false([], 'INVALID_ARGUMENT',$e->getMessage());
        } catch( Exception $e){
            return $this->getResponse()->falseServerError();
        }


        return $this->getResponse()->success($file);
    }

}