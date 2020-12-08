<?php


namespace app\controllers;

use app\forms\UserRequestForm;
use app\services\ProcessingDataService;
use app\services\User;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class V1Controller extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return 'api v1';
    }

    /**
     * @return array|string[]
     */
    public function actionAdd()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $token['key']               = Yii::$app->getRequest()->getHeaders()['Api-key'] ?? '';
        $token['id']                = Yii::$app->getRequest()->getHeaders()['Api-id'] ?? '';
        $userId                      = User::identityUser($token);

        if ($userId) {
            $request     = Yii::$app->getRequest()->post();
            $processData = new ProcessingDataService(UserRequestForm::class, $request, $userId);
            list($statusCode, $response) = $processData->processWriteData();
        } else {
            $statusCode = 403;
            $response   = ['message' => 'Not Authorization'];
        }

        Yii::$app->response->statusCode = $statusCode;
        return $response;
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $token['key']               = Yii::$app->getRequest()->getHeaders()['Api-key'] ?? '';
        $token['id']                = Yii::$app->getRequest()->getHeaders()['Api-id'] ?? '';
        $user                       = User::identityUser($token);

        if ($user) {
            $request = Yii::$app->getRequest()->post();
            $processData = new ProcessingDataService(UserRequestForm::class, $request, $user);
            list($statusCode, $response) = $processData->processGetData();
        } else {
            $statusCode = 403;
            $response   = ['message' => 'Not Authorization'];
        }

        Yii::$app->response->statusCode = $statusCode;
        return $response;
    }
}
