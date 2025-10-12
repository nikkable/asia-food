<?php

namespace backend\controllers;

use context\Commerce1C\interfaces\CommerceProcessorInterface;
use context\Commerce1C\models\CommerceRequest;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class CommerceMLController extends Controller
{
    public $enableCsrfValidation = false;

    private CommerceProcessorInterface $commerceProcessor;

    public function init()
    {
        parent::init();
        $this->commerceProcessor = Yii::$container->get(CommerceProcessorInterface::class);
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/plain; charset=utf-8');

        try {
            // Получаем тело запроса для файлов
            $content = file_get_contents('php://input');
            
            // Создаем объект запроса
            $request = CommerceRequest::fromArray($_GET, $content ?: null);
            
            // Обрабатываем запрос
            $response = $this->commerceProcessor->processRequest($request);
            
            // Устанавливаем код ответа
            Yii::$app->response->statusCode = $response->getHttpCode();
            
            // Возвращаем результат
            return $response->toString();
            
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            Yii::error('CommerceML error: ' . $e->getMessage(), __METHOD__);
            return "failure\nInternal server error";
        }
    }
}
