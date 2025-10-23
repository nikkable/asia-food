<?php
namespace frontend\controllers;

use GuzzleHttp\Client;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class CommercemlProxyController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Проксирует все запросы с /connectors/commerceml/ на backend CommerceMLController
     */
    public function actionIndex(): string
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/plain; charset=utf-8');

        $backendUrl = Yii::$app->params['backendUrl'];
        $target = rtrim($backendUrl, '/') . '/commerce-m-l/';

        $method = Yii::$app->request->method;
        $headers = Yii::$app->request->headers->toArray();
        $body = Yii::$app->request->rawBody;
        $query = Yii::$app->request->queryString;
        $url = $target . ($query ? ('?' . $query) : '');
        
        $client = new Client([
            'http_errors' => false,
        ]);

        // Преобразуем заголовки в формат для Guzzle
        $guzzleHeaders = [];
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'host') continue;
            if (strtolower($key) === 'authorization') continue;
            if (is_array($value)) $value = implode('; ', $value);
            $guzzleHeaders[$key] = $value;
        }

        // Проверяем и передаем HTTP Basic Auth
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $guzzleHeaders['Authorization'] = $_SERVER['HTTP_AUTHORIZATION'];
        }
        // Если нет в $_SERVER, проверяем заголовки Yii
        elseif (isset($headers['authorization'])) {
            $authHeader = is_array($headers['authorization']) ? $headers['authorization'][0] : $headers['authorization'];
            $guzzleHeaders['Authorization'] = $authHeader;
        }
        elseif (isset($headers['Authorization'])) {
            $authHeader = is_array($headers['Authorization']) ? $headers['Authorization'][0] : $headers['Authorization'];
            $guzzleHeaders['Authorization'] = $authHeader;
        }

        $options = [
            'headers' => $guzzleHeaders,
            'body' => $body,
        ];

        try {
            $response = $client->request($method, $url, $options);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            Yii::$app->response->statusCode = $statusCode;
            return $responseBody;
            
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return "failure\nProxy error: " . $e->getMessage();
        }
    }
}
