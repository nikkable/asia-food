<?php

namespace backend\controllers;

use context\Commerce1C\interfaces\CommerceProcessorInterface;
use repositories\Commerce1C\models\CommerceRequest;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class CommerceMLController extends Controller
{
    public $enableCsrfValidation = false;

    private CommerceProcessorInterface $commerceProcessor;

    public function init(): void
    {
        parent::init();
        $this->commerceProcessor = Yii::$container->get(CommerceProcessorInterface::class);
    }

    public function actionIndex(): string
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/plain; charset=utf-8');

        try {
            $content = file_get_contents('php://input');
            $request = CommerceRequest::fromArray($_GET, $content ?: null);
            $response = $this->commerceProcessor->processRequest($request);
            
            Yii::$app->response->statusCode = $response->getHttpCode();
            
            return $response->toString();
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return "failure\nInternal server error";
        }
    }
}
