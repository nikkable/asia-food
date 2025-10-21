<?php

namespace context\Commerce1C\services;

use context\Commerce1C\interfaces\CommerceAuthInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use repositories\Commerce1C\models\CommerceRequest;
use repositories\Commerce1C\models\CommerceResponse;
use context\AbstractService;

class CommerceAuthService extends AbstractService implements CommerceAuthInterface
{
    public function __construct(
        private CommerceSessionInterface $sessionService
    ) {}

    public function checkAuth(CommerceRequest $request): CommerceResponse
    {
        // Получаем данные авторизации из запроса
        $username = $_SERVER['PHP_AUTH_USER'] ?? null;
        $password = $_SERVER['PHP_AUTH_PW'] ?? null;


        // Если нет в $_SERVER, проверяем Authorization заголовок
        if (!$username && !$password) {
            // Проверяем через $_SERVER
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            
            // Если нет в $_SERVER, проверяем через Yii request
            if (!$authHeader) {
                $authHeader = \Yii::$app->request->headers->get('Authorization', '');
            }
            
            if ($authHeader && preg_match('/Basic\s+(.*)$/i', $authHeader, $matches)) {
                $credentials = base64_decode($matches[1]);
                if (strpos($credentials, ':') !== false) {
                    list($username, $password) = explode(':', $credentials, 2);
                }
            }
        }

        // Проверяем авторизацию
        if (!$this->validateCredentials($username, $password)) {
            return CommerceResponse::failure('Invalid credentials', 401);
        }

        // Создаем новую сессию
        $session = $this->sessionService->createSession();
        $this->sessionService->saveSession($session);
        
        \Yii::error("Created and saved session: " . $session->getSessionId(), __METHOD__);

        return CommerceResponse::authSuccess($session->getSessionId());
    }

    public function validateSession(string $sessionId): bool
    {
        $session = $this->sessionService->getSession($sessionId);

        if (!$session) {
            \Yii::error("Session not found: $sessionId", __METHOD__);
            return false;
        }
        
        \Yii::error("Session found, checking expiration", __METHOD__);

        // Проверяем не истекла ли сессия
        if ($session->isExpired()) {
            \Yii::error("Session expired: $sessionId", __METHOD__);
            $this->sessionService->deleteSession($sessionId);
            return false;
        }
        
        \Yii::error("Session is valid: $sessionId", __METHOD__);
        return true;
    }

    private function validateCredentials(?string $username, ?string $password): bool
    {
        // Используем доступы от 1С
        return $username === '12257247' && $password === 'dbf61458dc34319eda48ac71f0e12e63';
    }
}
