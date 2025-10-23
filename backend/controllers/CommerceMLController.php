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
            $content = '';
            $input = fopen('php://input', 'r');
            if ($input) {
                $totalRead = 0;
                while (!feof($input)) {
                    $chunk = fread($input, 8192); // Читаем по 8KB
                    if ($chunk === false) {
                        Yii::error("Failed to read chunk at position $totalRead", __METHOD__);
                        break;
                    }
                    $content .= $chunk;
                    $totalRead += strlen($chunk);
                    
                    if ($totalRead > 52428800) { // 50MB
                        Yii::warning("Content size exceeded 50MB, stopping read", __METHOD__);
                        break;
                    }
                }
                fclose($input);
                Yii::info("Total read: $totalRead bytes", __METHOD__);
            } else {
                Yii::error("Failed to open php://input", __METHOD__);
            }

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
