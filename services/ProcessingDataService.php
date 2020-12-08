<?php

namespace app\services;

use app\extensions\formatters\RequestFormatter;
use app\forms\UserRequestForm;
use app\models\UserData;

class ProcessingDataService
{
    protected $form;
    protected $data;
    protected $userId;

    public function __construct(string $form, array $data, int $userId)
    {
        $this->form = new $form();
        $this->data = $data;
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function processWriteData() :array
    {
        $form = new UserRequestForm();
        $form->setAttributes($this->data);

        if ($form->validate()) {
            $userData       = new UserData();
            $data           = RequestFormatter::prepareRequestData($form->getAttributes(), true);
            $data['userId'] = $this->userId;

            if ($userData->saveData($data)) {
                $statusCode = 200;
                $response   = ['message' => 'yes, you are good'];
            } else {
                $statusCode = 404;
                $response   = ['message' => 'Sorry, the technique is so stupid'];
            }
        } else {
            $statusCode = 422;
            $response   = $form->getErrors();
        }

        return [$statusCode, $response];
    }

    /**
     * @return array
     */
    public function processGetData() :array
    {
        if (UserRequestForm::fastCheck($this->data['key1'], $this->data['key2'])) {
            $statusCode     = 200;
            $data           = RequestFormatter::prepareRequestData($this->data);
            $data['userId'] = $this->userId;
            $data           = UserData::find()->where($data)->asArray()->all();
            $response       = RequestFormatter::prepareResponseData($data);
        } else {
            $statusCode = 422;
            $response   = ['message' => 'Unknown or incomplete data'];
        }

        return [$statusCode, $response];
    }
}